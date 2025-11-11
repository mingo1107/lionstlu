<?php

namespace frontend\assets;


use common\assets\BaseAsset;

class FormValidateAsset extends BaseAsset
{

    public $css = [
        'css/jquery-form-validator.css'
    ];
    public $js = [
        'js/jquery-form-validator.js'
    ];
    public $depends = [
        'frontend\assets\FrontendAsset'
    ];
}