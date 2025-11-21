<?php
/* @var $this yii\web\View */
/* @var $member common\models\MemberModel */

use yii\helpers\Html;

$this->title = '工具下載';
?>

<div class="download">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?= Html::encode($this->title) ?></h1>
                
                <div class="alert alert-info">
                    <strong>會員：<?= Html::encode($member->name) ?></strong>
                    <?php if ($remainingDays = $member->getRemainingDays()): ?>
                        <p>會員有效期至：<?= Html::encode($member->period_end) ?>（剩餘 <?= $remainingDays ?> 天）</p>
                    <?php endif; ?>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">可下載的工具</h3>
                    </div>
                    <div class="panel-body">
                        <p>這裡是工具下載區，僅限已開通且在有效期內的會員使用。</p>
                        <!-- 在這裡添加下載連結 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

