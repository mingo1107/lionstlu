<?php

namespace frontend\widget;


use common\models\CustomerServiceModel;
use yii\base\Widget;

class Service extends Widget
{
    /**
     * @var CustomerServiceModel
     */
    public $model;

    public function run()
    {
        return $this->render('service', ['model' => $this->model]);
    }
}