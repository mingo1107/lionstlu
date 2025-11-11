<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\assets\BackendAsset;
use backend\widget\InlineScript;
use backend\widget\Menu;
use yii\helpers\Html;

$this->title = '愛分享後台管理';
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
    <?php $this->head() ?>
    <link rel="shortcut icon" href="../../favicon.ico">
</head>
<body>
<?php $this->beginBody() ?>

<div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header"><a href="#"><img src="/image/logo.png" width="142" height="48"></a></li>
            </ul>
        </div>
    </nav>
    <div id="page-wrapper" class="gray-bg">
        <?= $content ?>
        <div class="footer">
        </div>

    </div>
</div>

<?php $this->endBody() ?>

<?php InlineScript::begin() ?>
<script>
    (function () {
        $('.js-void').click(function (e) {
            e.preventDefault();
        });

        $('.input-group.date').datetimepicker({
            format: 'YYYY-MM-DD',
            locale: "zh-TW"
        });

        $('#_select-all').click(function () {
            if (this.checked) {
                $('input[name^="_select"]').each(function () {
                    this.checked = true;
                });
            } else {
                $('input[name^="_select"').each(function () {
                    this.checked = false;
                });
            }
        });
    })();
</script>
<?php InlineScript::end(); ?>
</body>
</html>
<?php $this->endPage() ?>
