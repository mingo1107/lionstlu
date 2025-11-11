<?php
namespace backend\widget;

use yii\base\Widget;

class Menu extends Widget
{
    public function run()
    {
        return $this->render('menu');
    }
}