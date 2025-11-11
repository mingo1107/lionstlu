<?php

namespace common\models;


use PDO;
use yii\db\Expression;

/**
 * @property float $net
 * @property float $gross
 * @property float $shipping_fee
 * @property float $tax
 * @property StandardModel $standard
 */
class OrdersDetailModel extends OrdersDetail
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public function rules()
    {
        $rules = parent::rules();
        array_push($rules,
            [['orders_id', 'status'], 'required', 'on' => self::SCENARIO_CREATE],
            [['orders_id', 'status'], 'required', 'on' => self::SCENARIO_UPDATE]
        );
        return $rules;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->scenario == self::SCENARIO_CREATE || $this->scenario == self::SCENARIO_UPDATE) {
                if ($this->scenario == self::SCENARIO_CREATE) {
                    $this->create_time = new Expression('now()');
                } else if ($this->scenario == self::SCENARIO_UPDATE) {
                    $this->update_time = new Expression('now()');
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function getStandard()
    {
        return $this->hasOne(StandardModel::class, ['id' => 'standard_id']);
    }

    /**
     * 回補庫存
     * @param int $orderId
     */
    public static function rollbackStock(int $orderId)
    {
        $orderDetailList = self::findAll(["orders_id" => $orderId]);
        if (!empty($orderDetailList)) {
            foreach ($orderDetailList as $d) {
                StandardModel::updateSoldStock($d->standard_id, $d->quantity, true);
            }
        }
    }

    public static function deleteAllByOrdersId(int $orderId)
    {
        return self::getDb()->createCommand("DELETE FROM orders_detail WHERE orders_id = :orders_id", [":orders_id" => $orderId])->execute();
    }

    /**
     * @param int $orderId
     * @return array
     * @throws \yii\db\Exception
     */
    public static function findAllByOrderId(int $orderId)
    {
        $sql = "SELECT d.*, s.name AS standard_name, s.name2 AS standard_name2, p.name AS product_name, p.id as product_id FROM orders_detail d 
            INNER JOIN standard s ON d.standard_id = s.id 
            INNER JOIN product p ON p.id = s.product_id WHERE orders_id = :orders_id";
        return self::getDb()->createCommand($sql, [":orders_id" => $orderId])->queryAll(PDO::FETCH_OBJ);
    }
}