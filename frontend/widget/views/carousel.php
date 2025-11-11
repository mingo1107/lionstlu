<?php
/* @var $bannerList \common\models\BannerModel[] */

use ball\helper\File;
use common\models\MediaTrait;

?>
<div class="row mb-none">
    <div class="col-lg-12 pa0">
        <div id="carousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <?php for ($i = 0; $i < count($bannerList); ++$i): ?>
                    <li data-target="#carousel" data-slide-to="<?= $i ?>"
                        class="<?= $i == 0 ? 'active' : '' ?>"></li>
                <?php endfor; ?>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <?php for ($i = 0; $i < count($bannerList); ++$i): ?>
                    <div class="item<?= $i == 0 ? ' active' : '' ?>">
                        <a href="<?= $bannerList[$i]->link ?>">
                            <img class="img-responsive"
                                 src="<?= File::img(File::CATEGORY_BANNER,
                                     MediaTrait::serialize($bannerList[$i], 'media')->src) ?>"
                                 alt="<?= $bannerList[$i]->name ?>"/>
                        </a>
                    </div>
                <?php endfor; ?>
            </div>

            <?php if (count($bannerList) > 1): ?>
                <!-- Controls -->
                <a class="left carousel-control" href="#carousel"
                   role="button"
                   data-slide="prev">
                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel" role="button"
                   data-slide="next">
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                    <span class="sr-only">Next</span>
                </a>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="row desktop-none">
    <!--手機廣告_開始-->
    <div class="col-xs-12 pa0">
        <div id="carousel-row-1" class="carousel-1">
            <?php for ($i = 0; $i < count($bannerList); ++$i): ?>
                <div class="carousel-1-cell">
                    <a href="<?= $bannerList[$i]->link ?>">
                        <img class="img-responsive"
                             src="<?= File::img(File::CATEGORY_BANNER,
                                 MediaTrait::serialize($bannerList[$i], 'media_m')->src) ?>"
                             alt="<?= $bannerList[$i]->name ?>"/>
                    </a>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <!--手機廣告_結束-->

</div>
<!--row-end-->
