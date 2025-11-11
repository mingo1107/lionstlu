<?php

namespace frontend\widget;


use common\models\ArticleCategoryModel;
use yii\base\Widget;

class Category extends Widget
{

    public function run()
    {
        $categoryList = ArticleCategoryModel::findByStatus(ArticleCategoryModel::STATUS_ONLINE);
        return parent::render('category', ["categoryList" => $categoryList]);
    }
}