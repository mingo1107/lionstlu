<?php

use ball\helper\File;
use common\models\ArticleModel;
use common\models\MediaTrait;
use common\models\QuickLink;
use frontend\widget\Carousel;

/* @var $this yii\web\View */
/* @var $promoteArticleList \common\models\ArticleModel[] */
/* @var $latestArticleList \common\models\ArticleModel[] */
/* @var $productArticleList \common\models\ArticleModel[] */
/* @var $quickLinks \common\models\QuickLink[] */
?>
<!--banner_開始-->
<div class="container-fluid index-banner">
    <?= Carousel::widget() ?>
</div>
<!--banner_結束-->

<!--快速連結_開始-->
<?php if (!empty($quickLinks)): ?>
<div class="container con_area">
    <div class="icon_area row">
        <div class=" col-4" >
            <a target=”_blank”  href="https://lionstlu.org.tw/member/login">
            <img src="/images/icon1.png" width="120" height="120">
            登入 </a>
        </div>
        <?php if (!Yii::$app->user->isGuest): ?>
        <div class=" col-4" >
            <a target="_blank" href="#">
            <img src="/images/icon2.png" width="120" height="120">
            直播</a>
            </div>
        <?php endif; ?>
        <div class=" col-4" >
            <a  target="_blank" href="https://www.facebook.com/LionsUniversity/">
            <img src="/images/icon3.png" width="120" height="120">
            臉書</a>
            </div>
        <?php if (!Yii::$app->user->isGuest): ?>
        <div class=" col-4" >
            <a target="_blank"  href=" https://drive.google.com/drive/folders/1VOenFSsqeW-PQ1DOeIkCfP2Jb4u9hnGS?usp=sharing">
            <img src="/images/icon4.png" width="120" height="120">
            影片回放 </a>
            </div>
        <?php endif; ?>
        <div class=" col-4" >
            <a  target=”_blank” href="https://calendar.google.com/calendar/u/0?cid=dGx1QHlhZ293dXMuY29t">
            <img src="/images/icon5.png" width="120" height="120">
            行事曆</a>
            </div>
        <div class=" col-4" >
            <a target=”_blank”  href="https://docs.google.com/document/d/1aevnS6-A6CvhhV6QRQj-36MfDL3TZ85mSD4OzTFPqFI/edit?usp=sharing">
            <img src="/images/icon6.png" width="120" height="120">
            最新消息</a>
        </div>
    </div >
</div>
<?php endif; ?>
<!--快速連結_結束-->

<?php if (!empty($promoteArticleList)): ?>
    <!--焦點文章_開始-->
    <div class="focus-article-fluid">
        <div class="container">
            <h2 class="title">本週焦點文章
                <a href="/article/search?s=<?= ArticleModel::STATUS_PROMOTE ?>" class="btn btn0814 btn-black">
                    觀看更多<i class="fa fa-chevron-right ml5" aria-hidden="true"></i>
                </a>
            </h2>
            <!--焦點文章_開始-->
            <div class="row">
                <?php foreach ($promoteArticleList as $article): ?>
                    <!--文章_開始-->
                    <div class="col-md-4">
                        <div class="focus-article-card">
                            <a href="/article/detail?id=<?= $article->id ?>">
                                <img src="<?= File::img(File::CATEGORY_ARTICLE,
                                        MediaTrait::serialize($article,'cover_media')->src) ?>" width="600"
                                     alt="<?= $article->title ?>" title="<?= $article->title ?>"
                                     class="img-responsive"/>
                                <div class="p1020">
                                    <h3 class="title"><?= $article->title ?></h3>
                                    <p><?= mb_substr(strip_tags($article->content_0), 0, 25) . '...' ?></p>
                                    <div class="card-row">
                                        <div class="location" >
                                         <div style="width: 50%; float:left; "><i class=" fa fa-map-marker" aria-hidden="true" style="width:20px"></i><?= $article->share_location ?></div>

                                        <div style="width: 50%; float:left;">

                                            <div style=" float:right; text-align: center; "><i class="fa fa-share-alt" style="width:20px" ></i></i><?= $article->share_count ?> </div>

                                           <div style="  float:right; text-align: center; width: 80px; " ><i class="fa fa-eye" aria-hidden="true" style="width:20px; text-align: left;"></i><?= $article->views ?> 次</div>
                                       </div>
                                     </div>
                                     <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!--文章_結束-->
                <?php endforeach; ?>
            </div>
            <!--焦點文章_結束-->
        </div>
    </div>
    <!--焦點文章_結束-->
<?php endif ?>

<div class="container">
    <?php if (!empty($latestArticleList)): ?>
        <h2 class="title">最新文章
            <a href="/article/search?s=<?= ArticleModel::STATUS_ONLINE ?>" class="btn btn0814 btn-black">觀看更多
                <i class="fa fa-chevron-right ml5" aria-hidden="true"></i>
            </a>
        </h2>

        <!--最新文章_開始-->
        <div class="row">
            <?php foreach ($latestArticleList as $article): ?>
                <!--文章_開始-->
                <div class="col-md-4">
                    <div class="article-card">
                        <a href="/article/detail?id=<?= $article->id ?>">
                            <img src="<?= File::img(File::CATEGORY_ARTICLE,
                                MediaTrait::serialize($article,'cover_media')->src) ?>" width="600"
                                 alt="<?= $article->title ?>" title="<?= $article->title ?>"
                                 class="img-responsive"/>
                            <div class="p1020">
                                <h3 class="title"><?= $article->title ?></h3>
                                <p><?= mb_substr(strip_tags($article->content_0), 0, 25) . '...' ?></p>

                                    <div class="location" >
                                         <div style="width: 50%; float:left; "><i class=" fa fa-map-marker" aria-hidden="true" style="width:20px"></i><?= $article->share_location ?></div>

                                        <div style="width: 50%; float:left;">

                                            <div style=" float:right; text-align: center; "><i class="fa fa-share-alt" style="width:20px" ></i></i><?= $article->share_count ?> </div>

                                           <div style="  float:right; text-align: center; width: 80px; " ><i class="fa fa-eye" aria-hidden="true" style="width:20px; text-align: left;"></i><?= $article->views ?> 次</div>
                                       </div>
                                     </div>
                                     <div style="clear:both;"></div>

                            </div>
                        </a>
                    </div>
                </div>
                <!--文章_結束-->
            <?php endforeach; ?>
        </div>
        <!--最新文章_結束-->
    <?php endif ?>

    <?php if (!empty($productArticleList)): ?>
        <h2 class="title">產品相關文章
            <a href="/article/search?t=<?= ArticleModel::AD_PRODUCT ?>" class="btn btn0814 btn-black">
                觀看更多<i class="fa fa-chevron-right ml5" aria-hidden="true"></i>
            </a>
        </h2>
        <!--產品相關文章_開始-->
        <div class="row">
            <?php foreach ($productArticleList as $article): ?>
                <!--文章_開始-->
                <div class="col-md-4">
                    <div class="article-card">
                        <a href="/article/detail?id=<?= $article->id ?>">
                            <img src="<?= File::img(File::CATEGORY_ARTICLE,
                                MediaTrait::serialize($article,'cover_media')->src) ?>" width="600"
                                 alt="<?= $article->title ?>" title="<?= $article->title ?>"
                                 class="img-responsive"/>
                            <div class="p1020">
                                <h3 class="title"><?= $article->title ?></h3>
                                <p><?= mb_substr(strip_tags($article->content_0), 0, 25) . '...' ?></p>
                            </div>
                        </a>
                    </div>
                </div>
                <!--文章_結束-->
            <?php endforeach; ?>
        </div>
        <!--產品相關文章_結束-->
    <?php endif ?>
</div>
<!--container-end-->
