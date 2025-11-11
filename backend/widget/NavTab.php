<?php

namespace backend\widget;


use yii\base\Widget;

class NavTab extends Widget
{
    /**
     * @var array
     */
    public $links;

    public function run()
    {
        return $this->render('navtab', ['links' => $this->links]);
    }
}