<?php

use ball\helper\File;
use ball\order\OrderStatus;
use common\models\MediaTrait;
use common\models\ProductModel;
use frontend\widget\Breadcrumbs;
use frontend\widget\MemberNav;

/* @var $order common\models\OrdersModel */
/* @var $product common\models\ProductModel */
/* @var $standard common\models\StandardModel */
/* @var $orderDetail common\models\OrdersDetailModel */
/* @var $breadcrumbs array */

?>
<style type="text/css">
    .history-order-detail h4 {
        margin: 0 0 8px 0;
    }

    .payinfo-title {
        width: 640px;
        padding-top: 17px;
        padding-bottom: 10px;
        text-align: right;
        font-size: 16px;
    }

    .note-red {
        color: #e60012;
    }

    .payinfo-content {
        width: 170px;
        padding-top: 15px;
        padding-bottom: 10px;
        text-align: right;
        font-size: 16px;
        border-bottom: 1px solid #ddd;
    }

    .payinfo-content span {
        font-size: 18px;
        font-family: 'Francois One', sans-serif;
    }

    .back-order-list {
        display: none;
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .payinfo-row {
        padding-top: 20px;
        padding-bottom: 40px;
    }

    @media (max-width: 1200px) {
        .payinfo-title {
            width: 67%;
        }

        .payinfo-content {
            width: 30%;
            margin-right: 3%;
        }
    }

    @media (min-width: 320px) and (max-width: 480px) {
        .mc-main-body .panel .panel-body h4 {
            margin: 20px 0 8px 0;
        }

        .back-order-list {
            display: block;
        }

        .payinfo-title {
            width: 60%;
        }

        .payinfo-content {
            width: 40%;
            margin-right: 0;
        }

        .payinfo-row {
            padding-top: 0px;
            padding-bottom: 0px;
            padding-right: 20px;
        }
    }

    /*
        Generic Styling, for Desktops/Laptops
        */
    table.rwd-style1 {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    /* Zebra striping */
    table.rwd-style1 tr:nth-of-type(odd) {
        background: #fff;
    }

    table.rwd-style1 th {
        background: #717171;
        color: white;
        font-weight: bold;
    }

    table.rwd-style1 td, table.rwd-style1 th {
        padding: 6px;
        border: 1px solid #eee;
        text-align: left;
    }

    /*
        Max width before this PARTICULAR table gets nasty
        This query will take effect for any screen smaller than 760px
        and also iPads specifically.
        */
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Force table to not be like tables anymore */
        table.rwd-style1, table.rwd-style1 thead, table.rwd-style1 tbody, table.rwd-style1 th, table.rwd-style1 td, table.rwd-style1 tr {
            display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        table.rwd-style1 thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        table.rwd-style1 tr {
            border: 1px solid #eee;
        }

        table.rwd-style1 td {
            /* Behave  like a "row" */
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50%;
        }

        table.rwd-style1 td:before {
            /* Now like a table header */
            position: absolute;
            /* Top/left values mimic padding */
            top: 6px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
        }

        /*
                Label the data
                */
        table.rwd-style1 td:nth-of-type(1):before {
            content: "付款方式";
        }

        table.rwd-style1 td:nth-of-type(2):before {
            content: "付款狀態";
        }

        table.rwd-style1 td:nth-of-type(3):before {
            content: "運送方式";
        }

        table.rwd-style1 td:nth-of-type(4):before {
            content: "出貨狀態";
        }

        table.rwd-style1 td:nth-of-type(5):before {
            content: "實付金額";
        }
    }

    /*
        Generic Styling, for Desktops/Laptops
        */
    table.rwd-style2 {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    /* Zebra striping */
    table.rwd-style2 tr:nth-of-type(odd) {
        background: #fff;
    }

    table.rwd-style2 th {
        background: #717171;
        color: white;
        font-weight: bold;
    }

    table.rwd-style2 td, table.rwd-style2 th {
        padding: 6px;
        border: 1px solid #eee;
        text-align: left;
    }

    /*
        Max width before this PARTICULAR table gets nasty
        This query will take effect for any screen smaller than 760px
        and also iPads specifically.
        */
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Force table to not be like tables anymore */
        table.rwd-style2, table.rwd-style2 thead, table.rwd-style2 tbody, table.rwd-style2 th, table.rwd-style2 td, table.rwd-style2 tr {
            display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        table.rwd-style2 thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        table.rwd-style2 tr {
            border: 1px solid #eee;
        }

        table.rwd-style2 td {
            /* Behave  like a "row" */
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50%;
        }

        table.rwd-style2 td:before {
            /* Now like a table header */
            position: absolute;
            /* Top/left values mimic padding */
            top: 6px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
        }

        /*
                Label the data
                */
        table.rwd-style2 td:nth-of-type(1):before {
            content: "發票型式";
        }

        table.rwd-style2 td:nth-of-type(2):before {
            content: "發票抬頭";
        }

        table.rwd-style2 td:nth-of-type(3):before {
            content: "統一編號";
        }
    }

    /*
        Generic Styling, for Desktops/Laptops
        */
    table.rwd-style3 {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0px;
    }

    /* Zebra striping */
    table.rwd-style3 tr:nth-of-type(odd) {
        background: #fff;
    }

    table.rwd-style3 th {
        background: #717171;
        color: white;
        font-weight: bold;
    }

    table.rwd-style3 td, table.rwd-style3 th {
        padding: 6px;
        border: 1px solid #eee;
        text-align: left;
    }

    table.rwd-style3 td img {
        max-width: 120px;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    /*
        Max width before this PARTICULAR table gets nasty
        This query will take effect for any screen smaller than 760px
        and also iPads specifically.
        */
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Force table to not be like tables anymore */
        table.rwd-style3, table.rwd-style3 thead, table.rwd-style3 tbody, table.rwd-style3 th, table.rwd-style3 td, table.rwd-style3 tr {
            display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        table.rwd-style3 thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        table.rwd-style3 tr {
            border: 1px solid #eee;
        }

        table.rwd-style3 td {
            /* Behave  like a "row" */
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50%;
        }

        table.rwd-style3 td:before {
            /* Now like a table header */
            position: absolute;
            /* Top/left values mimic padding */
            top: 6px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
        }

        /*
                Label the data
                */
        table.rwd-style3 td:nth-of-type(1):before {
            content: "商品";
        }

        table.rwd-style3 td:nth-of-type(2):before {
            content: "品名";
        }

        table.rwd-style3 td:nth-of-type(3):before {
            content: "規格";
        }

        table.rwd-style3 td:nth-of-type(4):before {
            content: "顏色";
        }

        table.rwd-style3 td:nth-of-type(5):before {
            content: "數量";
        }

        table.rwd-style3 td:nth-of-type(6):before {
            content: "原價";
        }

        table.rwd-style3 td:nth-of-type(7):before {
            content: "優惠價";
        }
    }

    /*
        Generic Styling, for Desktops/Laptops
        */
    table.rwd-style4 {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    /* Zebra striping */
    table.rwd-style4 tr:nth-of-type(odd) {
        background: #fff;
    }

    table.rwd-style4 th {
        background: #717171;
        color: white;
        font-weight: bold;
    }

    table.rwd-style4 td, table.rwd-style4 th {
        padding: 6px;
        border: 1px solid #eee;
        text-align: left;
    }

    /*
        Max width before this PARTICULAR table gets nasty
        This query will take effect for any screen smaller than 760px
        and also iPads specifically.
        */
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Force table to not be like tables anymore */
        table.rwd-style4, table.rwd-style4 thead, table.rwd-style4 tbody, table.rwd-style4 th, table.rwd-style4 td, table.rwd-style4 tr {
            display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        table.rwd-style4 thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        table.rwd-style4 tr {
            border: 1px solid #eee;
        }

        table.rwd-style4 td {
            /* Behave  like a "row" */
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50%;
        }

        table.rwd-style4 td:before {
            /* Now like a table header */
            position: absolute;
            /* Top/left values mimic padding */
            top: 6px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
        }

        /*
                Label the data
                */
        table.rwd-style4 td:nth-of-type(1):before {
            content: "繳費時間";
        }

        table.rwd-style4 td:nth-of-type(2):before {
            content: "繳費金額";
        }

        table.rwd-style4 td:nth-of-type(3):before {
            content: "匯款銀行";
        }

        table.rwd-style4 td:nth-of-type(4):before {
            content: "匯款帳號";
        }
    }
</style>
<div class="container">
    <?= Breadcrumbs::widget(['links' => $breadcrumbs]) ?>
    <div class="row">
        <?= MemberNav::widget(['index' => 3]) ?>
        <!--會員中心_開始-->
        <!--內容_開始-->
        <div class="col-md-9 col-xs-12 mc-main-body">
            <div class="panel panel-default min-height-300">
                <div class="panel-heading">
                    <h3 class="panel-title">檢視訂單(訂單編號：<?= $order->no ?>)</h3>
                </div>
                <div class="panel-body">
                    <div class="row">

                        <!--交易資訊_開始-->
                        <div class="col-md-12 history-order-detail">
                            <h4>交易資訊</h4>
                            <table class="rwd-style1">
                                <thead>
                                <tr>
                                    <th>訂單狀態</th>
                                    <th>實付金額</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?= OrderStatus::$labels[$order->status] ?></td>
                                    <td>$<?= $order->gross ?>元</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--交易資訊_結束-->

                        <!--交易資訊_開始-->
                        <div class="col-md-12 history-order-detail">
                            <h4>ATM繳費資訊</h4>
                            <table class="rwd-style4">
                                <thead>
                                <tr>
                                    <th>繳費時間</th>
                                    <th>繳費金額</th>
                                    <th>匯款銀行</th>
                                    <th>匯款帳號</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>2016-05-24~2016-05-30</td>
                                    <td>$1,915元</td>
                                    <td>第一銀行（代碼 007）</td>
                                    <td>000-000-0000</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--交易資訊_結束-->

                        <!--發票資訊_開始-->
                        <div class="col-md-12 history-order-detail">
                            <h4>發票資訊</h4>
                            <table class="rwd-style2">
                                <thead>
                                <tr>
                                    <th>發票型式</th>
                                    <th>發票抬頭</th>
                                    <th>統一編號</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>二聯式發票</td>
                                    <td>xxxx有限公司</td>
                                    <td>23656352</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--發票資訊_結束-->

                        <!--訂單明細_開始-->
                        <div class="col-md-12 history-order-detail">
                            <h4>訂單明細</h4>
                            <table class="rwd-style3">
                                <thead>
                                <tr>
                                    <th>商品</th>
                                    <th>品名</th>
                                    <th>規格</th>
                                    <th>數量</th>
                                    <th>原價</th>
                                    <th>優惠價</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <img src="<?= File::img(File::CATEGORY_PRODUCT,
                                            MediaTrait::serialize($product, 'media')->src) ?>"
                                             width="626" class="img-responsive"></td>
                                    <td><?= $product->name ?></td>
                                    <td><?= $product->standard_type == ProductModel::STANDARD_TYPE_DOUBLE ?
                                            $standard->name . ' - ' . $standard->name2 : $standard->name ?></td>
                                    <td><?= $orderDetail->quantity ?></td>
                                    <td>$<?= $standard->original_price ?>元</td>
                                    <td>$<?= $standard->price ?>元</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--訂單明細_結束-->

                    </div>
                    <!--row-end-->

                    <div class="row payinfo-row">

                        <!--消費資訊_開始-->
                        <div class="col-md-9 col-sm-9 col-xs-9 payinfo-title">小計：</div>
                        <div class="col-md-2 col-sm-2 col-xs-2 payinfo-content"><span>$<?= $order->net ?></span> 元</div>
                        <div class="col-md-9 col-sm-9 col-xs-9 payinfo-title">運費：</div>
                        <div class="col-md-2 col-sm-2 col-xs-2 payinfo-content">
                            <span>$<?= $order->shipping_fee ?></span> 元
                        </div>
                        <div class="col-md-9 col-sm-9 col-xs-9 payinfo-title note-red">消費總金額：</div>
                        <div class="col-md-2 col-sm-2 col-xs-2 payinfo-content note-red">
                            <span>$<?= $order->gross ?></span> 元
                        </div>
                        <!--消費資訊_結束-->

                    </div>
                    <!--row-end-->

                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 back-order-list">
                    <a href="/order/index" class="btn btn-default btn-block btn-lg">
                        <i class="fa fa-angle-left mr5" aria-hidden="true"></i>返回訂單列表
                    </a>
                </div>
            </div>
            <!--row-end-->
        </div>
        <!--內容_結束-->
    </div>
</div>
