<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\widget\InlineScript;
use ball\util\Url;
use common\models\CustomerServiceModel;
use frontend\assets\FrontendAsset;
use frontend\widget\Category;
use yii\helpers\Html;

$this->title = '台灣獅子大學';
FrontendAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/images/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/images/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/images/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="/images/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="../../favicon.ico">

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-K23QP9BF');
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XDEKRLD0NL"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-XDEKRLD0NL');
    </script>

</head>

<body>


    <?php $this->beginBody() ?>
    <div id="fb-root"></div>
    <!--<div class="login-message">-->
    <!--    <div class="alert alert-default"><strong class="font-md">【王小明】您已登入成功!</strong></div>-->
    <!--</div>-->
    <!--網站外框_開始-->
    <div id="wrapper">

        <!--表頭_開始-->
        <header>
            <div class="top-search-bar">
                <form id="search-form-m" name="search-form-m" action="/article/search" method="get">
                    <div class="input-group">
                        <input type="text" id="keyword-m" name="keyword-m" class="form-control"
                            value="<?= trim(Yii::$app->request->get('keyword-m') ?? '') ?>" placeholder="請輸入關鍵字搜尋" />
                        <a href="#"><i class="fa fa-times" aria-hidden="true"></i></a>
                        <span class="input-group-btn">
                            <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                    <!-- /input-group -->
                </form>
            </div>
            <nav class="navbar navbar-default">
                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" id="bars" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <i class="fa fa-bars" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="navbar-toggle search-btn">
                            <i class="fa fa-search" aria-hidden="true"></i></button>
                        <?php if (!Yii::$app->user->isGuest): ?>
                            <button type="button" class="navbar-toggle">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <a class="navbar-brand" href="/">
                            <img src="/images/logo.png" width="142" height="48" title="台灣獅子大學" alt="台灣獅子大學"
                                class="img-responsive">
                        </a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <?php if (Yii::$app->user->isGuest): ?>
                                <li class="desktop-none">
                                    <!--                                <a class="js-void" data-fancybox data-type="iframe" data-src="/member/login">-->
                                    <a href="/member/login">
                                        <span class="mb-id"><i class="fa fa-user mr5" aria-hidden="true"></i>
                                            登入</span>
                                    </a>
                                </li>
                            <?php endif ?>
                            <!-- <li><a href=""><i class="fa fa-briefcase mr5" aria-hidden="true"></i>直播</a>
                            <a href="/cs/index"><i
                                        class="fa fa-briefcase mr5" aria-hidden="true"></i>合作提案</a> 
                        </li>
                        <li><a href=""><i class="fa fa-briefcase mr5" aria-hidden="true"></i>臉書</a>
                            <a href="/cs/index"><i
                                        class="fa fa-briefcase mr5" aria-hidden="true"></i>合作提案</a> 
                        </li>
                        <li><a href=""><i class="fa fa-briefcase mr5" aria-hidden="true"></i>錄影</a>
                            <a href="/cs/index"><i
                                        class="fa fa-briefcase mr5" aria-hidden="true"></i>合作提案</a> 
                        </li>
                        <li><a href=""><i class="fa fa-briefcase mr5" aria-hidden="true"></i>行事曆</a>
                             <a href="/cs/index"><i
                                        class="fa fa-briefcase mr5" aria-hidden="true"></i>合作提案</a> 
                        </li>
                        <li><a href=""><i class="fa fa-briefcase mr5" aria-hidden="true"></i>訊息</a>
                             <a href="/cs/index"><i
                                        class="fa fa-briefcase mr5" aria-hidden="true"></i>合作提案</a> 
                        </li>-->


                            <!-- <li>
                            <a href="/cs/index?c=<//?= CustomerServiceModel::CATEGORY_INVITE ?>"><i
                                        class="fa fa-bullhorn mr5" aria-hidden="true"></i>採訪邀約</a>
                        </li>-->
                            <li>
                                <a href="/cs/index?c=<//?= CustomerServiceModel::CATEGORY_SELF_RECOMMEND ?>"><i
                                        class="fa fa-pencil-square-o mr5" aria-hidden="true"></i>聯絡我們</a>
                            </li>
                            <form id="search-form-d" name="search-form-d" action="/article/search" method="get"
                                class="navbar-form navbar-left mb-none" role="search">
                                <div class="input-group">
                                    <input type="text" id="keyword-d" name="keyword-d" class="form-control"
                                        value="<?= trim(Yii::$app->request->get('keyword-d') ?? '') ?>"
                                        placeholder="請輸入關鍵字搜尋" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </form>
                        </ul>
                        <?php
                        // 調試信息（僅開發環境）
                        $isGuest = Yii::$app->user->isGuest;
                        $userId = Yii::$app->user->getId();
                        $identity = Yii::$app->user->identity;
                        if (YII_DEBUG && isset($_GET['debug'])) {
                            echo "<!-- DEBUG: isGuest=" . ($isGuest ? 'true' : 'false') . ", userId=" . ($userId ?: 'null') . ", identity=" . ($identity ? get_class($identity) : 'null') . " -->";
                        }
                        ?>
                        <?php if ($isGuest): ?>
                            <!--未登入_開始-->
                            <div class="navbar-btn navbar-right mb-none">
                                <a href="/member/login" class="btn btn0814 btn-success">登入/註冊
                                </a>
                            </div>
                            <!--未登入_結束-->
                        <?php else: ?>
                            <!--已登入_開始-->
                            <ul class="nav navbar-nav navbar-right">
                                <li class="desktop-none">
                                    <a href="/member/center"><i class="fa fa-pencil mr5" aria-hidden="true"></i>會員資料修改</a>
                                </li>
                                <li class="desktop-none">
                                    <a href="/member/service"><i class="fa fa-paper-plane mr5"
                                            aria-hidden="true"></i>聯絡客服</a>
                                </li>
                                <li class="desktop-none">
                                    <a href="/member/reply">
                                        <i class="fa fa-reply mr5" aria-hidden="true"></i>最新客服回覆
                                    </a>
                                </li>
                                <li class="desktop-none">
                                    <a href="/order/index">
                                        <i class="fa fa-search mr5" aria-hidden="true"></i>歷史訂單查詢
                                    </a>
                                </li>
                                <li class="mb-none">
                                    <span class="desktop-id"><?= Yii::$app->user->identity ? Yii::$app->user->identity->name : 'Unknown' ?></span>
                                </li>
                                <li class="mb-none"><a href="/member/center">會員中心</a></li>
                                <li class="mb-none"><a href="/site/logout">登出</a></li>
                                <li class="desktop-none"><a href="/site/logout"><i class="fa fa-sign-out mr5"
                                            aria-hidden="true"></i>登出</a>
                                </li>
                            </ul>
                            <!--已登入_結束-->
                        <?php endif ?>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.container-fluid -->
            </nav>
        </header>
        <!--表頭_結束-->


        <?= Category::widget() ?>

        <!--內容外框_開始-->
        <div id="body_content">
            <?= $content ?>
        </div>
        <!--container-end-->

    </div>
    <!--內容外框_結束-->

    <!--置底_開始-->
    <footer>
        <div class="footer-link-row">
            <div class="container">
                <div class="col-md-2 col-sm-3 col-xs-12"><img src="/images/logo.png" width="150" height="51" title="台灣獅子大學" alt="台灣獅子大學" class="img-responsive"></div>
                <div class="col-md-10 col-sm-9 col-xs-12">
                    <div class="row">
                        <div class="col-sm-4 col-xs-12">
                            <h4>關於</h4>
                            <ul class="footer-link">
                                <li><a href="<?php echo Url::to("/site/about") ?>">關於我們</a></li>
                                <li><a href="<?php echo Url::to("/site/policy") ?>">服務條款</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <h4>社群</h4>
                            <ul class="footer-link">
                                <li><a href="https://www.facebook.com/WindTalkNews/">台灣獅子大學粉絲團</a></li>
                                <li><a href="https://www.facebook.com/LionsClubsStory/">獅子說故事</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <h4>聯繫</h4>
                            <ul class="footer-link">
                                <li><a href="<?php echo Url::to("/cs/index") ?>">客服聯絡</a></li>
                                <li><a href="<?php echo Url::to("/cs/index") ?>">我要投稿</a></li>
                                <li><a href="<?php echo Url::to("/cs/index") ?>">合作提案</a></li>
                                <li><a href="<?php echo Url::to("/cs/index") ?>">採訪邀約</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-legal-row">
            <div class="container">
                <div class="col-md-12">
                    <p>台灣獅子大學 2025 ALL RIGHTS RESERVED</p>
                </div>
            </div>
        </div>
    </footer>
    <!--置底_結束-->

    <?php $this->endBody() ?>

    <?php InlineScript::begin() ?>
    <script>
        (function() {

            $('.input-group.date').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: "zh-TW"
            });

            $("[data-fancybox]").fancybox({
                caption: $(this).data('caption') || ''
            });

            var sendEvent = function(sel, step) {
                $(sel).trigger('next.m.' + step);
            };

            $("#carousel-row").flickity({
                cellAlign: 'left',
                /*齊左*/
                wrapAround: false,
                pageDots: false,
                contain: true,
                /*第一個區塊齊左*/
                autoPlay: false,
                /*自動播放*/
                initialIndex: 0 /*指定那一個為預設*/
            });

            // flickity
            $("#carousel-row-1").flickity({
                wrapAround: true,
            });

            // 搜尋.js
            $("button.search-btn").click(function() {
                $('.top-search-bar').animate({
                    top: '0px',
                    opacity: '1'
                });
            });

            $(".top-search-bar a").click(function() {
                $('.top-search-bar').animate({
                    top: '-53px',
                    opacity: '0'
                });
            });

            // 登入訊息js
            $('.login-message').animate({
                top: '10px',
                opacity: '1'
            }).delay(2500).fadeOut(2000);
        })();
    </script>
    <?php InlineScript::end(); ?>
</body>

</html>
<?php $this->endPage() ?>