<?php

namespace frontend\widget;


use common\models\ArticleCategoryModel;
use yii\base\Widget;

class Category extends Widget
{

    public function run()
    {
        // 檢查登入狀態，過濾掉需要登入但用戶未登入的分類
        $categoryList = ArticleCategoryModel::findByStatus(ArticleCategoryModel::STATUS_ONLINE, true);
        return parent::render('category', ["categoryList" => $categoryList]);
    }
}