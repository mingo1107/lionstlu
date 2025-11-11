<?php

use ball\order\OrderStatus;
use frontend\widget\Breadcrumbs;
use frontend\widget\MemberNav;
use frontend\widget\Paging;

/* @var $list common\models\OrdersModel[] */
/* @var $orderDetailList array */
/* @var $breadcrumbs array */
/* @var $start int */
/* @var $count int */

?>
<div class="container">
    <?= Breadcrumbs::widget(['links' => $breadcrumbs]) ?>
    <div class="row">
        <?= MemberNav::widget(['index' => 3]) ?>
        <!--會員中心_開始-->
        <!--內容_開始-->
        <div class="col-md-9 col-xs-12 mc-main-body">
            <div class="panel panel-default min-height-300">
                <div class="panel-heading">
                    <h3 class="panel-title">歷史訂單查詢</h3>
                </div>
                <div class="panel-body">

                    <!--訂單抬頭-開始-->
                    <div class="order-header-row">
                        <div class="order-info">
                            <div class="order-header-number">訂單編號</div>
                            <div class="order-header-date">訂單日期</div>
                            <div class="order-header-name">商品名稱</div>
                            <div class="order-header-shipping">訂單狀態</div>
                        </div>
                        <div class="order-header-view">檢視</div>
                    </div>
                    <!--訂單抬頭-開始-->
                    <?php foreach ($list as $o): ?>
                        <!--訂單內容-開始-->
                        <div class="order-body-row">
                            <div class="order-info">
                                <div class="order-body-number">
                                    <div class="field-title">訂單編號</div>
                                    <div class="field-content"><?= $o->no ?></div>
                                </div>
                                <div class="order-body-date">
                                    <div class="field-title">訂單日期</div>
                                    <div class="field-content"><?= date('Y-m-d', strtotime($o->create_time)) ?></div>
                                </div>
                                <div class="order-body-name">
                                    <div class="field-title">商品名稱</div>
                                    <div class="field-content"><?= $orderDetailList[$o->id][0]->product_name ?></div>
                                </div>
                                <div class="order-body-shipping">
                                    <div class="field-title">訂單狀態</div>
                                    <div class="field-content"><?= OrderStatus::$labels[$o->status] ?></div>
                                </div>
                            </div>
                            <div class="order-view">
                                <a href="/order/detail?o=<?= $o->no ?>" class="btn btn-success">檢視訂單</a>
                            </div>
                        </div>
                        <!--訂單內容-結束-->
                    <?php endforeach; ?>
                </div>
            </div>

            <!--分頁_開始-->
            <?= Paging::widget(['start' => $start, 'count' => $count]) ?>
            <!--分頁_結束-->

        </div>
        <!--內容_結束-->
    </div>
</div>
