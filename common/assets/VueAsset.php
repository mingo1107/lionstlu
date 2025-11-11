<?php


namespace common\assets;


use yii\web\View;

class VueAsset extends BaseAsset
{

    public $jsInlinePosition = [
        'position' => View::POS_HEAD
    ];

    public $css = [
        'https://cdn.jsdelivr.net/npm/bootstrap-vue@2.0.0-rc.22/dist/bootstrap-vue.min.css'
    ];

    public $js = [
        '//cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js',
//        '//cdn.jsdelivr.net/npm/bootstrap-vue@2.0.0-rc.22/dist/bootstrap-vue.common.min.js',
        '//cdn.jsdelivr.net/npm/bootstrap-vue@2.0.0-rc.22/dist/bootstrap-vue.common.js',
    ];
}