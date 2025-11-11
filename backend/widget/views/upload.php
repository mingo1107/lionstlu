<?php

use backend\assets\UploadAsset;
use ball\helper\File;


/* @var $auto bool */
/* @var $type int */
/* @var $id string */
/* @var $name string */
/* @var $fileId string */
/* @var $path string */
/* @var $wPath string */
/* @var $mPath string */
/* @var $category string */
/* @var $current string */
/* @var $wCurrent string */
/* @var $mCurrent string */
/* @var $img string */
/* @var $wImg string */
/* @var $mImg string */
/* @var $accept string */
/* @var $crop bool */
/* @var $dataUrl string */
/* @var $autoSubmit bool */
/* @var $autoRefresh bool */
/* @var $allowMultiple bool */
/* @var $showOriginFileOption bool */
/* @var $wFileId string */
/* @var $mFileId string */
/* @var $cropRatio string */

$hide = empty($path) ? 'hide' : '';
$hideWindow = empty ($wPath) ? 'hide' : 'card';
$hideMobile = empty ($mPath) ? 'hide' : 'card';

UploadAsset::register($this);
?>

<div class="col-12 col-md-6">
    <div class="row">
        <div class="col-12 col-sm-6 col-md-12 mb-3">
            <div id="<?php echo $fileId ?>-preview" class="file-preview <?php echo $hide ?>">

                <?php if ($type == File::TYPE_IMAGE) { ?>
                    <a id="<?php echo $fileId ?>-display-a"
                       href="<?php echo $path ?>" <?= !empty($path) && $type != File::TYPE_VIDEO ? 'data-fancybox target="_blank"' : '' ?>>
                        <img id="<?php echo $fileId ?>-display" class="img-responsive"
                             src="<?php echo $img ?>"/>
                    </a>
                    <?php
                } else if ($type == File::TYPE_VIDEO) { ?>
                    <video width="400" controls>
                        <source id="<?php echo $fileId ?>-display" src="<?php echo $path ?>"
                                type="video/<?= pathinfo($path, PATHINFO_EXTENSION) ?>">
                        Your browser does not support HTML5 video.
                    </video>
                <?php } else { ?>
                    <a id="<?php echo $fileId ?>-display-a"
                       href="<?php echo $path ?>" <?= !empty($path) && $type != File::TYPE_VIDEO ? 'data-fancybox target="_blank"' : '' ?>>Download</a>
                <?php } ?>

                <div id="<?php echo $fileId ?>-display-tool">
                    <?php if ($showOriginFileOption): ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger js-a js-img-delete"
                                    data-target="<?php echo $fileId ?>">
                                <?= 'delete' ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-12 mb-3">
            <div id="w_<?php echo $fileId ?>-preview" class="file-preview <?php echo $hideWindow ?>">

                <?php if ($type == File::TYPE_IMAGE && !empty ($wPath)) { ?>
                    <a id="w_<?php echo $fileId ?>-display-a"
                       href="<?php echo $wPath ?>" <?= !empty($wPath) && $type != File::TYPE_VIDEO ? 'data-fancybox target="_blank"' : '' ?>>
                        <img id="w_<?php echo "w_" . $fileId ?>-display" class="img-fluid card-img-top"
                             src="<?php echo $wImg ?>"/>
                    </a>
                    <?php
                } else if ($type == File::TYPE_VIDEO) { ?>
                    <video width="400" controls>
                        <source id="w_<?php echo $fileId ?>-display" src="<?php echo $wPath ?>"
                                type="video/<?= pathinfo($wPath, PATHINFO_EXTENSION) ?>">
                        Your browser does not support HTML5 video.
                    </video>
                <?php } else { ?>
                    <a id="w_<?php echo $fileId ?>-display-a"
                       href="<?php echo $wPath ?>" <?= !empty($wPath) && $type != File::TYPE_VIDEO ? 'data-fancybox target="_blank"' : '' ?>>Download</a>
                <?php } ?>

                <div id="<?php echo $fileId ?>-display-tool" class="card-body">
                    <div class="btn-group mr-2">
                        <?= "upload.desktop"; ?>
                    </div>
                    <div id="w_<?php echo $fileId ?>-display-crop"
                         class="<?= $type == File::TYPE_IMAGE ? '' : 'hide' ?> btn-group">
                        <button type="button" class="btn btn-primary js-a js-img-crop" data-crop="w"
                                data-target="w_<?php echo $fileId ?>">
                            <?= 'crop' ?>
                        </button>

                        <button type="button" class="btn btn-danger js-a js-img-delete"
                                data-target="w_<?php echo $fileId ?>">
                            <?= 'delete' ?>
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12 col-md-6 mb-3 pl-md-3">
    <div id="m_<?php echo $fileId ?>-preview" class="file-preview <?php echo $hideMobile ?>">

        <?php if ($type == File::TYPE_IMAGE && !empty($mPath)) { ?>
            <a id="m_<?php echo $fileId ?>-display-a"
               href="<?php echo $mPath ?>" <?= !empty($mPath) && $type != File::TYPE_VIDEO ? 'data-fancybox target="_blank"' : '' ?>>
                <img id="m_<?php echo $fileId ?>-display" class="img-fluid card-img-top"
                     src="<?php echo $mImg ?>"/>
            </a>
            <?php
        } else if ($type == File::TYPE_VIDEO) { ?>
            <video width="400" controls>
                <source id="m_<?php echo $fileId ?>-display" src="<?php echo $mPath ?>"
                        type="video/<?= pathinfo($mPath, PATHINFO_EXTENSION) ?>">
                Your browser does not support HTML5 video.
            </video>
        <?php } else { ?>
            <a id="m_<?php echo $fileId ?>-display-a"
               href="<?php echo $mPath ?>" <?= !empty($mPath) && $type != File::TYPE_VIDEO ? 'data-fancybox target="_blank"' : '' ?>>Download</a>
        <?php } ?>

        <div id="<?php echo $fileId ?>-display-tool" class="card-body">
            <div class="btn-group mr-2">
                <?= "upload.mobile" ?>
            </div>
            <div id="m_<?php echo $fileId ?>-display-crop"
                 class="<?= $type == File::TYPE_IMAGE ? '' : 'hide' ?> btn-group">
                <button type="button" class="btn btn-primary js-a js-img-crop" data-crop="m"
                        data-target="m_<?php echo $fileId ?>">
                    <?= 'crop' ?>
                </button>

                <button type="button" class="btn btn-danger js-a js-img-delete"
                        data-target="m_<?php echo $fileId ?>">
                    <?= 'delete' ?>
                </button>

            </div>
        </div>
    </div>
</div>

<div class="col-12 margin-bottom-1">
    <div id="<?php echo $fileId ?>-bar"></div>
    <input type="file" <?= $accept ?> id="<?php echo $fileId ?>"
           name="<?php echo $fileId ?>" class="js-upload"
           data-url="<?= $dataUrl ?>" <?= $allowMultiple ? "multiple" : "" ?>
           data-category="<?php echo $category ?>" data-auto="<?php echo $auto === true ? 1 : 0 ?>"
           data-current="<?php echo $current ?>"
           data-field="<?php echo $id ?>" data-auto-submit="<?= $autoSubmit ?>"
           data-auto-refresh="<?= $autoRefresh ?>"
           data-file-window="<?= $wFileId ?>"
           data-file-mobile="<?= $mFileId ?>"
        <?php if ($crop): ?>
            data-crop-window="1" data-crop-ratio-window="<?= $cropRatio ?>"
        <?php endif ?>
    />
    <input type="hidden" id="<?php echo $id ?>" name="<?php echo $name ?>" value="<?= $current ?>"/>
    <input type="hidden" id="<?php echo $id ?>UploadInfo" name="<?php echo $id ?>UploadInfo" value=""/>
    <input type="file" <?= $accept ?> id="w_<?php echo $fileId ?>"
           name="w_<?php echo $fileId ?>" class="hide js-upload" data-url="<?= $dataUrl ?>"
           data-category="<?php echo $category ?>" data-subtype="d"
           data-current="<?php echo $wCurrent ?>"
           data-field="w_<?php echo $id ?>" multiple/>
    <input type="hidden" id="w_<?php echo $id ?>" name="w_<?php echo $name ?>" value="<?= $wCurrent ?>"/>
    <img id="w_<?php echo $id ?>-file-origin" class="hide" data-file-name="<?php echo $current ?>"
         data-target="<?= $fileId ?>"
         src="<?= $path ?>"
         data-img="<?= $path == "" ? "" : "data:image/jpeg;base64," . base64_encode(@file_get_contents($path)) ?>"/>
</div>
<!-- crop fancybox -->

<div id="w_<?php echo $fileId ?>-crop" class="hide margin-5px">
    <div class="btn-group col-sm-12 col-md-12" role="group">
        <div class="col-md-6 col-sm-6">
            <button type="button" class="btn btn-primary js-crop-confirm"
                    data-target="<?php echo $fileId ?>">裁切圖片
            </button>
        </div>
    </div>
</div>


