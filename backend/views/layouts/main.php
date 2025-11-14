<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\assets\BackendAsset;
use backend\widget\InlineScript;
use backend\widget\Menu;
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
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div id="wrapper">
    <?= Menu::widget() ?>
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message"><?= Yii::$app->user->getIdentity() ? Yii::$app->user->getIdentity()->name : 'Unknown' ?></span>
                    </li>


                    <li>
                        <a href="/site/logout">
                            <i class="fa fa-sign-out"></i> Log out
                        </a>
                    </li>
                </ul>

            </nav>
        </div>
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

        $("[data-fancybox]").fancybox({
            caption: $(this).data('caption') || ''
        });
    })();
</script>
<?php InlineScript::end(); ?>
</body>
</html>
<?php $this->endPage() ?>
