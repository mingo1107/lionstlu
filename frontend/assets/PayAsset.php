<?php

namespace frontend\assets;


use common\assets\BaseAsset;

class PayAsset extends BaseAsset
{
    public $css = [
        '/css/pay.css',
    ];

    public $depends = [
        'frontend\assets\FrontendAsset'
    ];
}