<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\assets\BackendAsset;
use backend\widget\InlineScript;
use yii\helpers\Html;

$this->title = '台灣獅子大學後台管理';
BackendAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <style>
        body {
            background-color:#fafafa !important;
        }
    </style>
    <?php $this->head() ?>
    <link rel="shortcut icon" href="../../favicon.ico">
</head>
<body class="usb-gb" style="overflow-y: hidden;">
<?php $this->beginBody() ?>

<div id="wrapper">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div><?= $content ?></div>
    </div>
</div>

<?php $this->endBody() ?>

<?php InlineScript::begin() ?>
<script></script>
<?php InlineScript::end(); ?>
</body>
</html>
<?php $this->endPage() ?>
