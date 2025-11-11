<?php

namespace ball\order;


class OrderStatus
{
    // 未處理
    const UNPROCESSED = 0;
    // 未付款
    const UNPAID = 1;
    const PAID = 9;
    // 已出貨
    const SHIPPED = 2;
    // 已送達
    const ARRIVED = 3;
    // 出貨處理中
    const SHIPMENT_PROCESSING = 4;
    // 申請退貨
    const APPLY_RETURN = 5;
    // 延後出貨
    const POSTPONE = 6;
    // 訂單完成
    const COMPLETED = 7;
    // 可退貨
    const AVAILABLE_RETURN = 8;
    // 物流收單 訂單出貨
    const ACQUIRE_SHIPMENT = 10;
    // 申請換貨
    const APPLY_EXCHANGE = 13;
    // 可換貨
    const AVAILABLE_EXCHANGE = 14;
    // 不可退貨
    const NO_RETURN = 18;
    // 不可換貨
    const NO_EXCHANGE = 19;
    // 重新(補)出貨
    const REDELIVER = 21;
    // 訂單失效
    const INVALID = 22;
    // 換貨已送回
    const EXCHANGE_RETURN = 24;
    // 退貨已送回
    const RETURN = 25;
    // 貨已到門市
    const ARRIVED_STORE = 26;
    // 申請退單
    const APPLY_CANCEL = 27;
    // 物流配送異常
    const ERROR_DELIVERY = 28;
    // 客訴強迫退單
    const CUSTOMER_CANCEL = 32;
    // 店配已取貨
    const PICKED_STORE = 34;
    // 物流串接 轉退貨單
    const TRANSFER_RETURN = 35;
    // 退貨已送回超商
    const RETURN_STORE = 36;
    // 換貨已送回超商
    const EXCHANGE_STORE = 37;
    // 母單結單
    const MAIN_CLOSED = 52;
    // 母單失效
    const MAIN_INVALID = 54;
    // 退貨異常
    const ERROR_RETURN = 55;
    // 訂單刪除
    const DELETED = 99;

    // 部份商品已出貨
    const PARTIAL_SHIPPED = 102;
    // 部份商品已送達
    const PARTIAL_ARRIVED = 103;
    // 部分商品出貨處理中
    const PARTIAL_SHIPMENT_PROCESSING = 104;
    // 部分商品申請退貨
    const PARTIAL_APPLY_RETURN = 105;
    // 部份商品延後出貨
    const PARTIAL_POSTPONE = 106;
    // 部份商品可退貨
    const PARTIAL_AVAILABLE_RETURN = 108;
    // 部份商品物流收單 訂單出貨
    const PARTIAL_ACQUIRE_SHIPMENT = 110;
    // 部份商品申請換貨
    const PARTIAL_APPLY_EXCHANGE = 113;
    // 部份商品可換貨
    const PARTIAL_AVAILABLE_EXCHANGE = 114;
    // 部份商品不可退貨
    const PARTIAL_NO_RETURN = 118;
    // 部份商品不可換貨
    const PARTIAL_NO_EXCHANGE = 119;
    // 部份商品重新(補)出貨
    const PARTIAL_REDELIVER = 121;
    // 部份商品換貨已送回
    const PARTIAL_EXCHANGE_RETURN = 124;
    // 部份商品退貨已送回
    const PARTIAL_RETURN = 125;
    // 部份商品已到門市
    const PARTIAL_ARRIVED_STORE = 126;
    // 部份商品物流配送異常
    const PARTIAL_ERROR_DELIVERY = 128;
    // 部份商品店配已取貨
    const PARTIAL_PICKED_STORE = 134;
    // 部份商品物流串接 轉退貨單
    const PARTIAL_TRANSFER_RETURN = 135;
    // 部份商品退貨已送回超商
    const PARTIAL_RETURN_STORE = 136;
    // 部份商品換貨已送回超商
    const PARTIAL_EXCHANGE_STORE = 137;
    // 部份商品退貨異常
    const PARTIAL_ERROR_RETURN = 155;

    // 請自行確認
    const MANUAL_CHECK = 201;

    public static $partialStatus = [
        self::PARTIAL_SHIPPED,
        self::PARTIAL_ARRIVED,
        self::PARTIAL_SHIPMENT_PROCESSING,
        self::PARTIAL_APPLY_RETURN,
        self::PARTIAL_POSTPONE,
        self::PARTIAL_AVAILABLE_RETURN,
        self::PARTIAL_ACQUIRE_SHIPMENT,
        self::PARTIAL_APPLY_EXCHANGE,
        self::PARTIAL_AVAILABLE_EXCHANGE,
        self::PARTIAL_NO_RETURN,
        self::PARTIAL_NO_EXCHANGE,
        self::PARTIAL_REDELIVER,
        self::PARTIAL_EXCHANGE_RETURN,
        self::PARTIAL_RETURN,
        self::PARTIAL_ARRIVED_STORE,
        self::PARTIAL_ERROR_DELIVERY,
        self::PARTIAL_PICKED_STORE,
        self::PARTIAL_TRANSFER_RETURN,
        self::PARTIAL_RETURN_STORE,
        self::PARTIAL_EXCHANGE_STORE,
        self::PARTIAL_ERROR_RETURN,
    ];

    public static $labels = [
        // 未處理
        self::UNPROCESSED => '未處理',
        // 準備出貨
        self::UNPAID => '未付款',
        // 已送達
        self::PAID => '已付款',
        // 出貨處理中
        self::SHIPMENT_PROCESSING => '出貨處理中',
        // 已出貨
        self::SHIPPED => '已出貨',
        // 申請退貨
//        self::APPLY_RETURN => '申請退貨',
        // 延後出貨
//        self::POSTPONE => '延後出貨',
        // 訂單完成
//        self::COMPLETED => '訂單完成',
        // 可退貨
//        self::AVAILABLE_RETURN => '可退貨',
        // 物流收單，訂單出貨
//        self::ACQUIRE_SHIPMENT => '物流收單，訂單出貨',
        // 申請換貨
//        self::APPLY_EXCHANGE => '申請換貨',
        // 可換貨
//        self::AVAILABLE_EXCHANGE => '可換貨',
        // 不可退貨
//        self::NO_RETURN => 'order.no.return',
        // 不可換貨
//        self::NO_EXCHANGE => 'order.no.exchange',
        // 重新(補)出貨
//        self::REDELIVER => 'order.redeliver',
        // 訂單失效
//        self::INVALID => '訂單失效',
        // 換貨已送回
//        self::EXCHANGE_RETURN => 'order.exchange.return',
        // 退貨已送回
//        self::RETURN => 'order.return',
        // 貨已到門市
//        self::ARRIVED_STORE => 'order.arrived.store',
        // 申請退單
//        self::APPLY_CANCEL => 'order.apply.cancel',
        // 物流配送異常
//        self::ERROR_DELIVERY => 'order.error.delivery',
        // 客訴強迫退單
//        self::CUSTOMER_CANCEL => 'order.customer.cancel',
        // 店配已取貨
//        self::PICKED_STORE => 'order.picked.store',
        // 物流串接，轉退貨單'
//        self::TRANSFER_RETURN => 'order.transfer.return',
        // 退貨已送回超商
//        self::RETURN_STORE => 'order.return.store',
//        // 母單結單
//        self::MAIN_CLOSED => 'order.main.closed',
//        // 母單失效
//        self::MAIN_INVALID => 'order.main.invalid',
//        // 退貨異常
//        self::ERROR_RETURN => 'order.error.return',
//        // 訂單刪除
//        self::DELETED => 'order.deleted',
//        // 部份商品已出貨
//        self::PARTIAL_SHIPPED => 'order.partial.shipped',
//        // 部份商品已送達
//        self::PARTIAL_ARRIVED => 'order.partial.arrived',
//        // 部分商品出貨處理中
//        self::PARTIAL_SHIPMENT_PROCESSING => 'order.partial.shipment.processing',
//        // 部分商品申請退貨
//        self::PARTIAL_APPLY_RETURN => 'order.partial.apply.return',
//        // 部份商品延後出貨
//        self::PARTIAL_POSTPONE => 'order.partial.postpone',
//        // 部份商品可退貨
//        self::PARTIAL_AVAILABLE_RETURN => 'order.partial.available.return',
//        // 部份商品物流收單 訂單出貨
//        self::PARTIAL_ACQUIRE_SHIPMENT => 'order.partial.acquire.shipment',
//        // 部份商品申請換貨
//        self::PARTIAL_APPLY_EXCHANGE => 'order.partial.apply.exchange',
//        // 部份商品可換貨
//        self::PARTIAL_AVAILABLE_EXCHANGE => 'order.partial.available.exchange',
//        // 部份商品不可退貨
//        self::PARTIAL_NO_RETURN => 'order.partial.no.return',
//        // 部份商品不可換貨
//        self::PARTIAL_NO_EXCHANGE => 'order.partial.no.exchange',
//        // 部份商品重新(補)出貨
//        self::PARTIAL_REDELIVER => 'order.partial.redeliver',
//        // 部份商品換貨已送回
//        self::PARTIAL_EXCHANGE_RETURN => 'order.partial.exchange.return',
//        // 部份商品退貨已送回
//        self::PARTIAL_RETURN => 'order.partial.return',
//        // 部份商品已到門市
//        self::PARTIAL_ARRIVED_STORE => 'order.partial.arrived.store',
//        // 部份商品物流配送異常
//        self::PARTIAL_ERROR_DELIVERY => 'order.partial.error.delivery',
//        // 部份商品店配已取貨
//        self::PARTIAL_PICKED_STORE => 'order.partial.picked.store',
//        // 部份商品物流串接 轉退貨單
//        self::PARTIAL_TRANSFER_RETURN => 'order.partial.transfer.return',
//        // 部份商品退貨已送回超商
//        self::PARTIAL_RETURN_STORE => 'order.partial.return.store',
//        // 部份商品換貨已送回超商
//        self::PARTIAL_EXCHANGE_STORE => 'order.partial.exchange.store',
//        // 部份商品退貨異常
//        self::PARTIAL_ERROR_RETURN => 'order.partial.error.return',
//        // 請自行確認
//        self::MANUAL_CHECK => 'order.manual.check'
    ];

    public static $selectable = [
        self::SHIPPED,
        self::APPLY_EXCHANGE,
        self::PARTIAL_APPLY_EXCHANGE,
        self::PARTIAL_SHIPPED,
        self::RETURN,
        self::PARTIAL_RETURN,
        self::UNPROCESSED,
        self::SHIPMENT_PROCESSING,
        self::ACQUIRE_SHIPMENT,
    ];

    public static $selectableLabels = [
        self::SHIPPED => 'order.shipped',
        self::APPLY_EXCHANGE => 'order.apply.exchange',
        self::PARTIAL_APPLY_EXCHANGE => 'order.partial.apply.exchange',
        self::PARTIAL_SHIPPED => 'order.partial.shipped',
        self::RETURN => 'order.return',
        self::PARTIAL_RETURN => 'order.partial.return',
        self::UNPROCESSED => 'order.unprocessed',
        self::SHIPMENT_PROCESSING => 'order.shipment.processing',
        self::ACQUIRE_SHIPMENT => 'order.acquire.shipment',
    ];

    public static $frontend = [
        self::SHIPPED,
        self::ARRIVED,
        self::COMPLETED,
        self::APPLY_EXCHANGE,
        self::PARTIAL_APPLY_EXCHANGE,
        self::PARTIAL_SHIPPED,
        self::RETURN,
        self::PARTIAL_RETURN,
        self::UNPROCESSED,
    ];

    public static $frontendLabels = [
        self::SHIPPED => 'order.shipped',
        self::ARRIVED => 'order.arrived',
        self::COMPLETED => 'order.completed',
        self::APPLY_EXCHANGE => 'order.apply.exchange',
        self::PARTIAL_APPLY_EXCHANGE => 'order.partial.apply.exchange',
        self::PARTIAL_SHIPPED => 'order.partial.shipped',
        self::RETURN => 'order.return',
        self::PARTIAL_RETURN => 'order.partial.return',
        self::UNPROCESSED => 'order.unprocessed',
    ];
}