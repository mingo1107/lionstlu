<?php

use ball\helper\File;
use common\models\ArticleModel;
use common\models\MediaTrait;
use frontend\widget\ArticleDefault;
use frontend\widget\ArticleProduct;
use frontend\widget\ArticleVote;
use frontend\widget\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $article \common\models\ArticleModel */
/* @var $breadcrumbs array */
$mediaList = MediaTrait::serialize($article, 'media', 8);

$id = $_GET['id'];

$httpProtocol = 'http';
if(!empty($_SERVER['REDIRECT_HTTPS']) && $_SERVER['REDIRECT_HTTPS'] == 'on'){
    $httpProtocol = 'https';
}

$actual_link = $httpProtocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//echo "<pre>";var_dump($_SERVER);
//echo $actual_link;
?>
<div class="container">
    <?= Breadcrumbs::widget(['links' => $breadcrumbs]) ?>
    <div class="row">

        <!--左側_開始-->
        <div class="col-md-8 col-sm-7 col-xs-12">
            <div class="row">

                <!--文章_開始-->
                <div class="col-md-12 col-sm-12">

                    <div class="article-detail">

                        <h1><?= $article->title ?></h1>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?=urlencode($actual_link)?>" target="_blank"
                            onClick="shareCount()">
                            分享Facebook
                        </a>｜
                        <a target="_blank" href="https://social-plugins.line.me/lineit/share?url=<?=urlencode($actual_link)?>?openExternalBrowser=1"
                            onClick="shareCount()">分享Line</a>
                        <br>
                        <?php for ($i = 0; $i < 8; ++$i): ?>
                            <?php if (!empty($mediaList[$i]->src)): ?>
                                <img src="<?= File::img(File::CATEGORY_ARTICLE, $mediaList[$i]->src) ?>"
                                     alt="<?= $article->title ?>"
                                     class="img-responsive"/>
                            <?php endif; ?>
                            <?php if (!empty($article->{"content_$i"})): ?>
                                <div><?= $article->{"content_$i"} ?></div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                </div>
                <!--文章_結束-->


            </div><!--row_end-->
        </div>
        <!--左側_結束-->

        <?php switch ($article->ad_type):
            case ArticleModel::AD_PRODUCT:
                echo ArticleProduct::widget(['article' => $article]);
                break;
            case ArticleModel::AD_VOTE:
                echo ArticleVote::widget(['article' => $article]);
                break;
            default:
                echo ArticleDefault::widget(['article' => $article]);
        endswitch ?>


    </div><!--end of .row-->
</div><!--end of .container-->

<script type="text/javascript">
    function shareCount(){

        let csrfName = $('meta[name=csrf-param]').prop('content');
        let csrfToken = $('meta[name=csrf-token]').prop('content');

        $.ajax({
            type: "POST",
            url: "<?php echo Yii::$app->getUrlManager()->createUrl('/article/xhr-share')  ; ?>",
            data: {
                id: <?=$id?>,
                '_csrf-frontend': csrfToken
            },
            success: function (data) {
                //alert(data);
            },
            error: function (exception) {
                //alert(exception);
            }
        })


        // $.post('xhr-share', $(this).serialize(), function (data) {
            

        // }, 'json');
    }
</script>




