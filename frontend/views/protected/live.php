<?php
/* @var $this yii\web\View */
/* @var $member common\models\MemberModel */

use yii\helpers\Html;

$this->title = '直播';
?>

<div class="live">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?= Html::encode($this->title) ?></h1>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">線上直播</h3>
                    </div>
                    <div class="panel-body">
                        <p>歡迎 <?= Html::encode($member->name) ?>，這裡是直播區。</p>
                        <!-- 在這裡添加直播內容 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

