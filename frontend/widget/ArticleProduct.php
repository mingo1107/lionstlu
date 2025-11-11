<?php

namespace frontend\widget;


use common\models\ArticleModel;
use common\models\ProductModel;
use common\models\StandardModel;
use yii\base\Widget;

class ArticleProduct extends Widget
{
    /**
     * @var ArticleModel
     */
    public $article;

    public function run()
    {
        $product = ProductModel::findOnline($this->article->ad_id);
        $standardList = [];
        $standardInfo = [];
        if (!empty($product)) {
            $standardList = StandardModel::findByProductId($product->id, false);
            foreach ($standardList as $s) {
                $standardInfo[$s->id] = [
                    'original_price' => $s->original_price,
                    'price' => $s->price
                ];
            }
        }
        return $this->render('article-product', ['product' => $product, 'standardList' => $standardList,
            'standardInfo' => $standardInfo]);
    }
}