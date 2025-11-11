<?php

namespace backend\assets;


use common\assets\BaseAsset;

class BackendAsset extends BaseAsset
{
    public $css = [
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css1',
        '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/open-iconic/1.1.1/font/css/open-iconic-bootstrap.css',
        '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.1.20/jquery.fancybox.min.css',
        'css/animate.css',
        'css/style.css',
        'css/skin.css',
        'css/extension.css',
        'css/plugins/dataTables/datatables.min.css',
        // date time picker
        '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css'
    ];
    public $js = [
        'js/common.js',
        '//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
        'js/jquery.touchSwipe.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.1.20/jquery.fancybox.min.js',
        'js/plugins/metisMenu/jquery.metisMenu.js',
        'js/plugins/slimscroll/jquery.slimscroll.min.js',
        'js/plugins/dataTables/datatables.min.js',
        'js/inspinia.js',
        'js/plugins/pace/pace.min.js',
        // date time picker
        '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment-with-locales.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'
    ];
    public $depends = [
    ];
}