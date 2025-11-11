<?php

namespace frontend\widget;


use common\models\BannerModel;
use yii\base\Widget;

class Carousel extends Widget
{
    public function run()
    {
        $bannerList = BannerModel::findByTypeAndStatus(BannerModel::TYPE_SLIDE, BannerModel::STATUS_ONLINE);
        return $this->render('carousel', ['bannerList' => $bannerList]);
    }
}