<?php

namespace backend\assets;


use common\assets\BaseAsset;

class FormValidateAsset extends BaseAsset
{

    public $css = [
        'css/jquery-form-validator.css'
    ];
    public $js = [
        '/js/jquery-form-validator.js'
    ];
    public $depends = [
        'backend\assets\BackendAsset'
    ];
}