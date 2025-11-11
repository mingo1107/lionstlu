<?php

namespace common\models;

/**
 * @property float $total
 * @property float $shipment_fee 物流手續費
 * @property float $fee 代收手續費
 */
class OrdersStatusFlowModel extends OrdersStatusFlow
{
    const USER_MEMBER = 'member';
}