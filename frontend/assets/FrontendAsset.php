<?php

namespace frontend\assets;


use common\assets\BaseAsset;

class FrontendAsset extends BaseAsset
{
    public $css = [
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
        '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/open-iconic/1.1.1/font/css/open-iconic-bootstrap.css',
        '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.1.20/jquery.fancybox.min.css',
        // date time picker
        '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css',
        '/css/flickity.min.css',
        '/css/icomoon/style.css',
        '/css/style.css?v=3',
        '/css/style-ext.css',
    ];
    public $js = [
        '/js/common.js?v=2',
        '//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
        '/js/jquery.touchSwipe.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.1.20/jquery.fancybox.min.js',
        // date time picker
        '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment-with-locales.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js',
//        'js/ie-emulation-modes-warning.js',
        '/js/docs.min.js',
        '/js/ie10-viewport-bug-workaround.js',
        '/js/multi-step-modal.js',
        '/js/flickity.pkgd.min.js',
        '//connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v2.12&appId=131829996970512'
    ];
    public $depends = [
    ];
}