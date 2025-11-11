<?php

namespace frontend\widget;


use yii\base\Widget;

class MemberNav extends Widget
{

    public $index = 0;

    public function run()
    {
        return $this->render('member-nav', ['index' => $this->index]);
    }
}