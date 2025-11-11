<?php

use ball\helper\File;
use common\models\MediaTrait;

/* @var $bannerList \common\models\BannerModel[] */
/* @var $articleList \common\models\ArticleModel[] */
?>
<!--側欄_開始-->
<div class="col-md-4 col-sm-4 col-xs-12 scrollspy">

    <?php foreach ($bannerList as $b): ?>
        <div class="ad-row-img">
            <a href="<?= $b->link ?>">
                <img src="<?= File::img(File::CATEGORY_BANNER,
                    MediaTrait::serialize($b, 'media')->src) ?>"
                     alt="<?= $b->name ?>" title="<?= $b->name ?>" width="370">
            </a>
        </div>
    <?php endforeach; ?>
    <div class="row">

        <div class="col-sm-12">

            <!--廣告_開始-->
            <?php foreach ($articleList as $a): ?>
                <div class="article-list-col">

                    <div class="col-md-4">
                        <div class="article-list-img">
                            <a href="/article/detail?id=<?= $a->id ?>">
                            <img src="<?= File::img(File::CATEGORY_ARTICLE,
                                MediaTrait::serialize($a, 'cover_media')->src) ?>"
                                 alt="<?= $a->title ?>" width="626" class="img-responsive"/>
                            </a>
                        </div>
                    </div>

                    <div class="col-md-8 article-list-content">
                        <h3 class="title"><a href="/article/detail?id=<?= $a->id ?>"><?= $a->title ?></a></h3>
                        <div class="article-list-other-info">
                            <span class="mr5">
                                <i class="fa fa-clock-o mr5"
                                   aria-hidden="true"></i><?= date('Y-m-d', strtotime($a->create_time)) ?></span>
                            <span><i class="fa fa-eye mr5" aria-hidden="true"></i><?= $a->views ?>人</span>
                        </div>
                    </div>

                </div>
                <!--廣告_結束-->
            <?php endforeach; ?>
        </div>


    </div><!--row_結束-->


</div>
<!--側欄_結束-->
