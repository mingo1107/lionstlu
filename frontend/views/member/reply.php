<?php

use frontend\widget\Breadcrumbs;
use frontend\widget\MemberNav;

/* @var $breadcrumbs array */
/* @var $start int */
/* @var $count int */
/* @var $list common\models\CustomerServiceModel[] */
/* @var $logList common\models\CustomerServiceLogModel[] */
?>
<div class="container">
    <?= Breadcrumbs::widget(['links' => $breadcrumbs]) ?>
    <div class="row">
        <?= MemberNav::widget(['index' => 2]) ?>
        <!--內容_開始-->
        <div class="col-md-9 col-xs-12 mc-main-body">

            <div class="panel panel-default min-height-300">
                <div class="panel-heading">
                    <h3 class="panel-title">最新客服回覆</h3>
                </div>
                <div class="panel-body">
                    <?php foreach ($list as $model): ?>
                        <!--問題_開始-->
                        <div class="faq-row">

                            <div class="question-row">
                                <div class="faq-time"><i class="fa fa-clock-o mr5" aria-hidden="true"></i>發問時間:2015/02/02
                                </div>
                                <h3><?= $model->title ?></h3>
                            </div>
                            <?php if (empty($logList[$model->id])): ?>
                                <div class="no-ans-row">
                                    <i class="fa fa-exclamation-triangle mr5" aria-hidden="true"></i>感謝您的問題留言,客服人員將於24小時內回覆
                                </div>
                            <?php else: ?>
                                <?php foreach ($logList[$model->id] as $log): ?>
                                    <div class="ans-row">
                                        <div class="faq-time">
                                            <i class="fa fa-reply mr5"
                                               aria-hidden="true"></i>回覆時間:<?= $log->create_time ?>
                                        </div>
                                        <p><?= nl2br($log->content) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif ?>
                        </div>
                        <!--問題_結束-->
                    <?php endforeach; ?>

                </div>
            </div>

        </div>
        <!--內容_結束-->
    </div>
</div>
