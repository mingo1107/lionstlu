<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\widget\InlineScript;
use frontend\assets\FrontendAsset;
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
    <link rel="shortcut icon" href="/images/favicon.png">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<div id="fb-root"></div>


<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '{your-app-id}',
      cookie     : true,
      xfbml      : true,
      version    : '{api-version}'
    });
      
    FB.AppEvents.logPageView();   
      
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

<?php $this->beginBody() ?>

<!--<div class="login-message">-->
<!--    <div class="alert alert-default"><strong class="font-md">【王小明】您已登入成功!</strong></div>-->
<!--</div>-->
<!--網站外框_開始-->
<div id="wrapper">
    <!--會員系統-開始-->
    <?= $content ?>
    <!--會員系統-結束-->

</div>
<!--網站外框_結束-->

<?php $this->endBody() ?>

<?php InlineScript::begin() ?>
<script>
    (function () {

        $('.input-group.date').datetimepicker({
            format: 'YYYY-MM-DD',
            locale: "zh-TW"
        });

        $("[data-fancybox]").fancybox({
            caption: $(this).data('caption') || ''
        });

        var sendEvent = function (sel, step) {
            $(sel).trigger('next.m.' + step);
        };
    })();
</script>
<?php InlineScript::end(); ?>
</body>
</html>
<?php $this->endPage() ?>
