<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "orders_status_flow".
 *
 * @property int $id
 * @property int $orders_id 訂單id
 * @property int $status 訂單狀態
 * @property string $orders_detail_id_list array format, 這筆出貨的訂單明細id列表
 * @property string $shipment_no 物流單號
 * @property string $total
 * @property string $create_user
 * @property string $create_time
 * @property string $update_user
 * @property string $update_time
 * @property string $note 備註
 * @property string $shipment_fee 物流手續費
 * @property string $fee 代收手續費
 * @property string $vendor_status 廠商拋檔狀態
 * @property string $vendor_id EC廠商代號
 */
class OrdersStatusFlow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_status_flow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orders_id', 'status'], 'integer'],
            [['orders_detail_id_list', 'create_user', 'create_time'], 'required'],
            [['total', 'shipment_fee', 'fee'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['note'], 'string'],
            [['orders_detail_id_list'], 'string', 'max' => 512],
            [['shipment_no'], 'string', 'max' => 128],
            [['create_user', 'update_user'], 'string', 'max' => 32],
            [['vendor_status', 'vendor_id'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orders_id' => 'Orders ID',
            'status' => 'Status',
            'orders_detail_id_list' => 'Orders Detail Id List',
            'shipment_no' => 'Shipment No',
            'total' => 'Total',
            'create_user' => 'Create User',
            'create_time' => 'Create Time',
            'update_user' => 'Update User',
            'update_time' => 'Update Time',
            'note' => 'Note',
            'shipment_fee' => 'Shipment Fee',
            'fee' => 'Fee',
            'vendor_status' => 'Vendor Status',
            'vendor_id' => 'Vendor ID',
        ];
    }
}
