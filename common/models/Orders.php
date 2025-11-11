<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $member_id
 * @property string $no
 * @property int $status
 * @property string $payment_id
 * @property string $shipment_id
 * @property string $net
 * @property string $gross
 * @property string $payment_fee 金流手續費
 * @property string $shipping_fee 物流手續費
 * @property string $tax
 * @property string $email
 * @property string $name
 * @property string $mobile
 * @property string $country
 * @property string $city
 * @property string $district
 * @property string $address
 * @property string $zip
 * @property string $receiver_email
 * @property string $receiver_name
 * @property string $receiver_mobile
 * @property string $receiver_country
 * @property string $receiver_city
 * @property string $receiver_district
 * @property string $receiver_address
 * @property string $receiver_zip
 * @property string $receive_time 預計收貨日
 * @property string $card_digit 卡號末四碼
 * @property string $invoice_no
 * @property int $invoice_type 發票類型
 * @property string $invoice_name
 * @property string $invoice_country
 * @property string $invoice_city
 * @property string $invoice_district
 * @property string $invoice_address
 * @property string $note
 * @property string $create_time
 * @property string $update_time
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'status', 'invoice_type'], 'integer'],
            [['no', 'email', 'name', 'mobile', 'country', 'city', 'district', 'address', 'zip', 'create_time'], 'required'],
            [['net', 'gross', 'payment_fee', 'shipping_fee', 'tax'], 'number'],
            [['receive_time', 'create_time', 'update_time'], 'safe'],
            [['note'], 'string'],
            [['no', 'mobile', 'country', 'city', 'district', 'receiver_mobile', 'receiver_country', 'receiver_city', 'receiver_district', 'receiver_zip', 'invoice_no', 'invoice_name', 'invoice_country', 'invoice_city', 'invoice_district'], 'string', 'max' => 32],
            [['payment_id', 'shipment_id'], 'string', 'max' => 64],
            [['email', 'address', 'receiver_email', 'receiver_address', 'invoice_address'], 'string', 'max' => 512],
            [['name', 'receiver_name'], 'string', 'max' => 256],
            [['zip'], 'string', 'max' => 8],
            [['card_digit'], 'string', 'max' => 4],
            [['no'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'no' => 'No',
            'status' => 'Status',
            'payment_id' => 'Payment ID',
            'shipment_id' => 'Shipment ID',
            'net' => 'Net',
            'gross' => 'Gross',
            'payment_fee' => 'Payment Fee',
            'shipping_fee' => 'Shipping Fee',
            'tax' => 'Tax',
            'email' => 'Email',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'country' => 'Country',
            'city' => 'City',
            'district' => 'District',
            'address' => 'Address',
            'zip' => 'Zip',
            'receiver_email' => 'Receiver Email',
            'receiver_name' => 'Receiver Name',
            'receiver_mobile' => 'Receiver Mobile',
            'receiver_country' => 'Receiver Country',
            'receiver_city' => 'Receiver City',
            'receiver_district' => 'Receiver District',
            'receiver_address' => 'Receiver Address',
            'receiver_zip' => 'Receiver Zip',
            'receive_time' => 'Receive Time',
            'card_digit' => 'Card Digit',
            'invoice_no' => 'Invoice No',
            'invoice_type' => 'Invoice Type',
            'invoice_name' => 'Invoice Name',
            'invoice_country' => 'Invoice Country',
            'invoice_city' => 'Invoice City',
            'invoice_district' => 'Invoice District',
            'invoice_address' => 'Invoice Address',
            'note' => 'Note',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
