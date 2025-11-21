<?php

namespace backend\controllers;


use ball\api\ResponseCode;
use ball\helper\File;
use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\ArticleCategoryModel;
use common\models\ArticleModel;
use common\models\ProductModel;
use common\models\VoteModel;
use common\models\AccessUserModel;
use common\models\AccessRoleModel;
use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

class ArticleController extends BackendController
{
    protected $actionLabel = '文章';

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ContentNegotiator::class,
                'only' => ['xhr-select'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ]
        ]);
    }

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword', 'ad_type']);
        $accessUser = AccessUserModel::findOne(['user_id' => Yii::$app->user->getIdentity()->id]);
        if($accessUser->role_id==7){
            $search['user_id'] = Yii::$app->user->getIdentity()->id;
        }
        $list = ArticleModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = ArticleModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }

    public function actionCreate()
    {
        $model = new ArticleModel([
            'scenario' => ArticleModel::SCENARIO_CREATE
        ]);
        $categoryList = ArticleCategoryModel::find()->all();
        if ($model->load(Yii::$app->request->post())) {
            $model->views = (empty($model->views)) ? 500 : $model->views;
            $model->share_count = (empty($model->share_count)) ? 1 : $model->share_count;
            $model->user_id = Yii::$app->user->getIdentity()->id;
            //views


            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('建立成功');
            }
            return $this->redirect(['index' . $this->queryString]);
        } else {

            $accessUser = AccessUserModel::findOne(['user_id' => Yii::$app->user->getIdentity()->id]);
            $roleAuthority = AccessRoleModel::roleAuthority($accessUser->role_id);
            //$need_verify = ($roleAuthority == 'COLUMNIST') ? 1 : 0;

            return $this->render('create', ['model' => $model, 'categoryList' => $categoryList, 'roleAuthority' => $roleAuthority]);
        }
    }

    public function actionUpdate()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = ArticleModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $categoryList = ArticleCategoryModel::find()->all();
        $model->scenario = ArticleModel::SCENARIO_UPDATE;
        $coverMedia = $model->serializeMedia('cover_media');
        $mediaList = $model->serializeMedia('media');
        $item = null;
        if ($model->ad_type == ArticleModel::AD_VOTE || $model->ad_type == ArticleModel::AD_PRODUCT) {
            if ($model->ad_type == ArticleModel::AD_VOTE) {
                $item = VoteModel::findOne(['id' => $model->ad_id]);
            } else if ($model->ad_type == ArticleModel::AD_PRODUCT) {
                $item = ProductModel::findOne(['id' => $model->ad_id]);
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('更新成功');
            }
            return $this->redirect(Yii::$app->request->referrer);
        } else {

            $accessUser = AccessUserModel::findOne(['user_id' => Yii::$app->user->getIdentity()->id]);
            $roleAuthority = AccessRoleModel::roleAuthority($accessUser->role_id);
            //$need_verify = ($roleAuthority == 'COLUMNIST') ? 1 : 0;

            return $this->render('update', ['model' => $model, 'item' => $item,
                'mediaList' => $mediaList, 'coverMedia' => $coverMedia, 'categoryList' => $categoryList, 'roleAuthority'=> $roleAuthority] );
        }
    }

    public function actionDelete()
    {
        $id = intval(Yii::$app->request->get('id'));
        ArticleModel::deleteAll(['id' => $id]);
        HtmlHelper::setMessage('刪除成功');
        return $this->redirect(['index' . $this->queryString]);
    }


    public function actionSelect()
    {
        $this->layout = 'iframe';
        $type = intval(Yii::$app->request->get('type'));
        $articleId = intval(Yii::$app->request->get('articleId', 0));
        $product = null;
        if (!empty($articleId)) {
            $article = ArticleModel::findOne(['id' => $articleId]);
        }
        if ($type == ArticleModel::AD_VOTE || $type == ArticleModel::AD_PRODUCT) {
            $start = Pagination::getOffset();
            $search = SQLHelper::buildSearchQuery(['status', 'keyword']);
            if ($type == ArticleModel::AD_VOTE) {
                $list = VoteModel::query($search);
                $vote = null;
                if (!empty($article)) {
                    $vote = VoteModel::findOne(['id' => $article->ad_id]);
                }
                return $this->render('vote', [
                    'list' => $list, 'start' => $start, 'vote' => $vote
                ]);
            } else if ($type == ArticleModel::AD_PRODUCT) {
                $list = ProductModel::query($search);
                $product = null;
                if (!empty($article)) {
                    $product = ProductModel::findOne(['id' => $article->ad_id]);
                }
                return $this->render('product', [
                    'list' => $list, 'start' => $start, 'product' => $product
                ]);
            }
        }
        return false;
    }

    public function actionXhrSelect()
    {
        $type = intval(Yii::$app->request->post('type'));
        $id = intval(Yii::$app->request->post('id'));
        if ($type == ArticleModel::AD_VOTE) {
            $vote = VoteModel::findOne(['id' => $id]);
            if (!empty($vote)) {
                return $vote;
            } else {
                return ResponseCode::errors(ResponseCode::ERROR_FAILED, 'No data');
            }
        } else if ($type == ArticleModel::AD_PRODUCT) {
            $product = ProductModel::findOne(['id' => $id]);
            if (!empty($product)) {
                return $product;
            } else {
                return ResponseCode::errors(ResponseCode::ERROR_FAILED, 'No data');
            }
        } else {
            return ResponseCode::errors(ResponseCode::ERROR_FAILED, 'error params');
        }
    }

    public function actionGenpicsee()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = ArticleModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        //$model->scenario = ArticleModel::SCENARIO_PICSEELINK;


        $url = "https://api.pics.ee/v1/links?access_token=7db21d0952a99013c3991e5908dced817a7d1827";
        $postdata = [
            "domain" => "lionstlu.org.tw",
            "url" => "https://lionstlu.org.tw/article/detail?id=".$id,
            "externalId" => strval($id)
        ];
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch); 
        curl_close($ch);

        $outputJson = json_decode($output, true);

        if(empty($outputJson['data']['picseeUrl'])){
            HtmlHelper::setError('產生短網址失敗');
        }
        else{
            $model->picsee_link = $outputJson['data']['picseeUrl'];
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('產生短網址成功');
            }
        }
        return $this->redirect(['index']);
    }
}
