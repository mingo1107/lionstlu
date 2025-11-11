<?php

use ball\helper\File;
use common\models\MediaTrait;
use frontend\widget\Paging;

/* @var $this yii\web\View */
/* @var $list \common\models\ArticleModel[] */
/* @var $start int */
/* @var $count int */
/* @var $category \common\models\ArticleCategoryModel */
/* @var $randomArticleList \common\models\ArticleModel[] */
/* @var $randomBannerList \common\models\BannerModel[] */
?>
<div class="container article-list-row">

    <h2 class="title"><?= $category->name ?></h2>
    <div style="margin-right: -30px;
    margin-left: -30px;">
        <!--文章列表_開始-->
        <div class="col-md-9">
            <div class="">

            <?php if (!empty($list)): ?>
                <?php foreach ($list as $article): ?>
                    <!--文章_開始-->
                    <div class=" col-md-4" >
                        <div class="article-list-col">
                            <div class="article-list-img">
                                <a href="/article/detail?id=<?= $article->id ?>">
                                    <img src="<?= File::img(File::CATEGORY_ARTICLE, MediaTrait::serialize($article, 'cover_media')->src) ?>"
                                         alt="<?= $article->title ?>" title="<?= $article->title ?>" width="600"
                                         class="img-responsive">
                                </a>
                            </div>
                       

                        <div class=" article-list-content" >
                            <h3 class="title">
                                <a href="/article/detail?id=<?= $article->id ?>"><?= $article->title ?></a>
                            </h3>
                            <p><?= mb_substr(strip_tags($article->content_0), 0, 60) ?></p>
                            <div class="article-list-other-info">
                                <span class="mr5"><i class="fa fa-clock-o mr5"
                                                     aria-hidden="true"></i><?= date('Y-m-d', strtotime($article->create_time)) ?></span>
                               
                            </div>
                             <div class="article-list-other-info">
                                <span class="mr5"><i class=" fa fa-map-marker" aria-hidden="true" style="width:20px"></i><?= $article->share_location ?></span>
                                <span class="mr5"><i class="fa fa-heart-o" style="width:20px" ></i></i><?= $article->share_count ?></span>
                                <span class="mr5"><i class="fa fa-eye" aria-hidden="true" style="width:20px; text-align: left;"></i><?= $article->views ?> 次</span>
                             </div>
                        </div>

                       


                       </div>
                    </div>
                    <!--文章_結束-->
                <?php endforeach ?>
                <?= Paging::widget(['start' => $start, 'count' => $count]) ?>
            <?php else: ?>
                無相關文章，<a href="/">回首頁</a>
            <?php endif; ?>
           </div>
        </div>
        <!--文章列表_結束-->


        <!--側欄_開始-->
        <div class="col-md-3">

            <?php foreach ($randomBannerList as $b): ?>
                <div class="ad-row-img">
                    <a href="<?= $b->link ?>">
                        <img src="<?= File::img(File::CATEGORY_BANNER,
                            MediaTrait::serialize($b, 'media')->src) ?>"
                             alt="<?= $b->name ?>" title="<?= $b->name ?>" width="370">
                    </a>
                </div>
            <?php endforeach; ?>

            <?php foreach ($randomArticleList as $a): ?>
                <div class="ad-row-article">
                    <a href="/article/detail?id=<?= $a->id ?>">
                        <img src="<?= File::img(File::CATEGORY_ARTICLE,
                            MediaTrait::serialize($a, 'cover_media')->src) ?>"
                             alt="<?= $a->title ?>" width="626" class="img-responsive"/>
                        <div class="p1020">
                            <h2><?= $a->title ?></h2>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <!--側欄_結束-->
    </div><!--row-end-->

</div><!--container-end-->
