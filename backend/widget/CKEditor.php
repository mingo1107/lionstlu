<?php

namespace backend\widget;


use yii\base\Widget;

class CKEditor extends Widget
{
//    public $type = 'active';
    public $model;
    public $attribute;
    public $options = [];

    public function run()
    {
        return $this->render('ckeditor', [
//            'type' => $this->type,
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => $this->options
        ]);
    }
}