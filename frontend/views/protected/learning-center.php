<?php
/* @var $this yii\web\View */
/* @var $member common\models\MemberModel */

use yii\helpers\Html;

$this->title = '學習中心';
?>

<div class="learning-center">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?= Html::encode($this->title) ?></h1>
                
                <div class="alert alert-success">
                    <strong>歡迎，<?= Html::encode($member->name) ?>！</strong>
                    <p>您的會員狀態：<?= Html::encode($member->getAccessStatus()) ?></p>
                    <?php if ($remainingDays = $member->getRemainingDays()): ?>
                        <p>剩餘有效天數：<strong><?= $remainingDays ?></strong> 天</p>
                    <?php endif; ?>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">學習資源</h3>
                    </div>
                    <div class="panel-body">
                        <p>這裡是學習中心的內容，僅限已開通且在有效期內的會員查看。</p>
                        <!-- 在這裡添加您的學習資源內容 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

