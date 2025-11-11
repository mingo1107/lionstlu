<?php

namespace backend\assets;


use common\assets\BaseAsset;

class UploadAsset extends BaseAsset
{
    public $css = [
        '//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.1/croppie.min.css'
    ];
    public $js = [
        '//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/vendor/jquery.ui.widget.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/blueimp-load-image/2.18.0/load-image.all.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/javascript-canvas-to-blob/3.14.0/js/canvas-to-blob.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/jquery.iframe-transport.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/jquery.fileupload.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/jquery.fileupload-process.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/jquery.fileupload-audio.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/jquery.fileupload-image.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/jquery.fileupload-video.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/jquery.fileupload-validate.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.1/croppie.min.js',
        '/js/upload.js',

    ];
    public $depends = [
        'backend\assets\BackendAsset'
    ];
}