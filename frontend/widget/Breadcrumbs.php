<?php

namespace frontend\widget;


use Yii;
use yii\base\Widget;

class Breadcrumbs extends Widget
{
    /**
     * @var array
     */
    public $links;
    /**
     * @var array
     */
    private $homeLink;

    public function init()
    {
        parent::init();
        $this->homeLink = [
            'label' => '首頁',
            'url' => Yii::$app->homeUrl,
        ];
    }

    public function run()
    {
        return $this->render('breadcrumbs', ['homeLink' => $this->homeLink, 'links' => $this->links]);
    }
}