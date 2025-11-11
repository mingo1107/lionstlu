<?php


namespace frontend\widget;


use common\models\ArticleModel;
use common\models\BannerModel;
use yii\base\Widget;

class ArticleDefault extends Widget
{
    public $article;

    public function run()
    {
        $bannerList = BannerModel::findAllRandomByType(BannerModel::TYPE_BANNER, 1);
        $articleList = ArticleModel::findAllRandomExcludeId($this->article->id, 5);
        return $this->render('article-default', [
            'bannerList' => $bannerList,
            'articleList' => $articleList
        ]);
    }
}