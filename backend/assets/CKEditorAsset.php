<?php

namespace backend\assets;


use common\assets\BaseAsset;

class CKEditorAsset extends BaseAsset
{
    public $js = [
        '//cdn.ckeditor.com/4.8.0/full/ckeditor.js',
        'js/ckeditor-custom.js',
    ];
}