<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "standard".
 *
 * @property int $id
 * @property int $product_id
 * @property string $name
 * @property string $name2 雙規名稱
 * @property string $sn 規格編號
 * @property int $status
 * @property float $original_price
 * @property float $price
 * @property float $shipping_fee
 * @property int $stock 庫存
 * @property int $sold_quantity 已售數量
 * @property string $intro
 * @property string $media
 * @property int $sort
 * @property string $create_time
 * @property string $update_time
 */
class Standard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'standard';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'name', 'sn', 'create_time'], 'required'],
            [['product_id', 'status', 'stock', 'sold_quantity', 'sort'], 'integer'],
            [['original_price', 'price', 'shipping_fee'], 'number'],
            [['intro', 'media'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'name2'], 'string', 'max' => 256],
            [['sn'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'name' => 'Name',
            'name2' => 'Name2',
            'sn' => 'Sn',
            'status' => 'Status',
            'original_price' => 'Original Price',
            'price' => 'Price',
            'shipping_fee' => 'Shipping Fee',
            'stock' => 'Stock',
            'sold_quantity' => 'Sold Quantity',
            'intro' => 'Intro',
            'media' => 'Media',
            'sort' => 'Sort',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
