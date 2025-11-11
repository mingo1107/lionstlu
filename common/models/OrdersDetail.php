<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "orders_detail".
 *
 * @property int $id
 * @property int $standard_id
 * @property int $orders_id
 * @property int $tax_type 商品稅務類型
 * @property int $status
 * @property string $quantity 訂購數量
 * @property string $net
 * @property string $gross
 * @property string $shipping_fee
 * @property string $tax
 * @property string $note
 * @property string $create_time
 * @property string $update_time
 */
class OrdersDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['standard_id', 'orders_id', 'tax_type', 'status'], 'integer'],
            [['quantity', 'net', 'gross', 'shipping_fee', 'tax'], 'number'],
            [['note'], 'string'],
            [['create_time'], 'required'],
            [['create_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'standard_id' => 'Standard ID',
            'orders_id' => 'Orders ID',
            'tax_type' => 'Tax Type',
            'status' => 'Status',
            'quantity' => 'Quantity',
            'net' => 'Net',
            'gross' => 'Gross',
            'shipping_fee' => 'Shipping Fee',
            'tax' => 'Tax',
            'note' => 'Note',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
