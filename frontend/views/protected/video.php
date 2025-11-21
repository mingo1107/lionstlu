<?php
/* @var $this yii\web\View */
/* @var $member common\models\MemberModel */

use yii\helpers\Html;

$this->title = '影片回放';
?>

<div class="video">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?= Html::encode($this->title) ?></h1>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">影片回放</h3>
                    </div>
                    <div class="panel-body">
                        <p>歡迎 <?= Html::encode($member->name) ?>，這裡是影片回放區。</p>
                        <!-- 在這裡添加影片列表 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

