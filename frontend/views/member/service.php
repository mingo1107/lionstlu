<?php

use frontend\widget\Breadcrumbs;
use frontend\widget\MemberNav;
use frontend\widget\Service;

/* @var $breadcrumbs array */
/* @var $category int */
/* @var $model common\models\CustomerServiceModel */
?>
<div class="container">
    <?= Breadcrumbs::widget(['links' => $breadcrumbs]) ?>
    <div class="row">
        <?= MemberNav::widget(['index' => 1]) ?>
        <?= Service::widget(['category' => $category, 'model' => $model]) ?>
    </div>
</div>
