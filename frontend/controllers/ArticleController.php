<?php

namespace frontend\controllers;


use ball\helper\File;
use ball\api\ResponseCode;
use ball\helper\Pagination;
use ball\util\HttpUtil;
use common\models\ArticleCategoryModel;
use common\models\ArticleModel;
use common\models\BannerModel;
use common\models\VoteModel;
use common\models\VoteOptionModel;
use common\models\VoteRecordModel;
use common\models\MediaTrait;
use Yii;
use yii\db\Expression;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class ArticleController extends FrontendController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ContentNegotiator::class,
                'only' => ['xhr-vote'],
                'formats' => [
                    'html/text' => Response::FORMAT_JSON
                ],
            ]
        ]);
    }

    public function actionDetail()
    {
        $id = intval(yii::$app->request->get('id'));
        $article = ArticleModel::findOneOnlineById($id);
        if (empty($article)) {
            return $this->redirect(['/site/index']);
        }
        ArticleModel::updateViewCount($article->id);
        $category = ArticleCategoryModel::findOne(['id' => $article->category_id]);
        $breadcrumbs = [
            ['url' => '/article/category?id=' . $category->id, 'label' => $category->name]
        ];


        if(!empty($article->title)){
            \Yii::$app->view->registerMetaTag(
                [
                    'property' => 'og:title',
                    'content' => $article->title
                ]
            );
        }
        if(!empty($article->og_description)){
            \Yii::$app->view->registerMetaTag(
                [
                    'name' => 'description',
                    'content' => $article->og_description
                ]
            );
            \Yii::$app->view->registerMetaTag(
                [
                    'property' => 'og:description',
                    'content' => $article->og_description
                ]
            );
        }
        if(!empty($article->og_keywords)){
            \Yii::$app->view->registerMetaTag(
                [
                    'name' => 'keyword',
                    'content' => $article->og_keywords
                ]
            );
        }

        if(!empty($article->cover_media)){
            $image = MediaTrait::serialize($article, 'cover_media', 1);
            if(!empty($image->src)){
                $og_image = File::img(File::CATEGORY_ARTICLE, $image->src);
                \Yii::$app->view->registerMetaTag(
                    [
                        'property' => 'og:image',
                        'content' => $og_image
                    ]
                );

                list($width, $height, $type, $attr) = getimagesize($og_image);
                $imagesize = getimagesize($og_image);
                
                \Yii::$app->view->registerMetaTag(
                    [
                        'property' => 'og:image:width',
                        'content' => $width
                    ]
                );
                \Yii::$app->view->registerMetaTag(
                    [
                        'property' => 'og:image:height',
                        'content' => $height
                    ]
                );

            }
                
        }

        return $this->render('detail', ['article' => $article, 'breadcrumbs' => $breadcrumbs]);
    }

    public function actionCategory()
    {
        $start = Pagination::getOffset();
        $categoryId = intval(yii::$app->request->get('id'));
        $category = ArticleCategoryModel::findOne(['status' => ArticleCategoryModel::STATUS_ONLINE, 'id' => $categoryId]);
        if (empty($category)) {
            return $this->redirect(['/site/index']);
        }
        $list = ArticleModel::search([
            'category' => $category->id
        ], Pagination::PAGE_SIZE, $start);

        $count = ArticleModel::countSearch([
            'category' => $category->id
        ]);
        $randomBannerList = BannerModel::findAllRandomByType(BannerModel::TYPE_BANNER, 1);
        $randomArticleList = ArticleModel::findAllRandomExcludeId(0, 3);
        return $this->render('category', ['list' => $list, "start" => $start,
            'count' => $count, 'category' => $category, 'randomArticleList' => $randomArticleList
            , 'randomBannerList' => $randomBannerList]);
    }

    public function actionSearch()
    {
        $start = Pagination::getOffset();
        $category = yii::$app->request->get('c');
        $type = yii::$app->request->get('t');
        $status = yii::$app->request->get('s');
        $randomBannerList = BannerModel::findAllRandomByType(BannerModel::TYPE_BANNER, 1);
        $randomArticleList = ArticleModel::findAllRandomExcludeId(0, 3);
        $keyword = !empty(yii::$app->request->get('keyword-d')) ?
            yii::$app->request->get('keyword-d') : yii::$app->request->get('keyword-m');

        $list = ArticleModel::search([
            'category' => $category,
            'type' => $type,
            'keyword' => $keyword,
            'status' => $status
        ], Pagination::PAGE_SIZE, $start);

        $count = ArticleModel::countSearch([
            'category' => $category,
            'type' => $type,
            'keyword' => $keyword,
            'status' => $status
        ]);

        $searchText = ArticleModel::searchText([
            'category' => $category,
            'type' => $type,
            'keyword' => $keyword,
            'status' => $status
        ]);

        return $this->render('search', [
            'list' => $list, 
            "start" => $start,
            'count' => $count, 
            'searchText' => $searchText, 
            'randomArticleList' => $randomArticleList, 
            'randomBannerList' => $randomBannerList
        ]);
    }

    public function actionXhrVote()
    {
        $voteId = intval(yii::$app->request->post('vote_id'));
        $optionId = intval(yii::$app->request->post('option'));
        if (!VoteModel::isOnline($voteId)) {
            return ResponseCode::errors(ResponseCode::ERROR_FAILED, 'This vote is offline');
        }

        $voteInfo = VoteModel::findOnline($voteId);


        $optionList = VoteOptionModel::findAllByVoteId($voteId);
        $options = [];
        foreach ($optionList as $o) {
            array_push($options, $o->id);
        }
        if (!in_array($optionId, $options)) {
            return ResponseCode::errors(ResponseCode::ERROR_FAILED, "Unknown option '$optionId'");
        }

        $memberId = yii::$app->user->isGuest ? 0 : yii::$app->user->getId();
        if($memberId==0){
            return ResponseCode::errors(ResponseCode::ERROR_NEED_LOGIN, "Please Login");
        }

        //判斷會員是否投票過
        //會員每天能投一票
        if($voteInfo->vote_limit == 'daily'){
            $voteExist = VoteRecordModel::findByVoteAndDateAndMemberId($voteId, $memberId, date('Y-m-d'));
            if(!empty($voteExist)){
                return ResponseCode::errors(ResponseCode::ERROR_VOTE_EXISTS_DAILY, "Only Vote Daily");
            }
        }
        //會員只能投一票
        else{ //if($voteInfo->vote_limit == 'once'){
            $voteExist = VoteRecordModel::findByVoteAndMemberId($voteId, $memberId);
            if(!empty($voteExist)){
                return ResponseCode::errors(ResponseCode::ERROR_VOTE_EXISTS, "Only Vote Once");
            }
        }


        $model = new VoteRecordModel();
        $model->ip = HttpUtil::ip();
        $model->create_time = new Expression('now()');
        $model->member_id = $memberId;
        $model->vote_id = $voteId;
        $model->option_id = $optionId;
        $model->save();
        VoteOptionModel::updateCount($optionId);
        return ResponseCode::success();
    }

    public function actionXhrShare()
    {
        $id = intval(yii::$app->request->post('id'));
        if (!ArticleModel::findOne($id)) {
            return ResponseCode::errors(ResponseCode::ERROR_FAILED, 'This article is not exist');
        }

        ArticleModel::updateShareCount($id);

        return json_encode([
            'status' => true
        ]);
    }
}
