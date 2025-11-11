<?php

namespace common\models;


use PDO;
use yii\db\Expression;

/**
 * Class StandardModel
 * @package common\models
 * @property ProductModel $product
 */
class StandardModel extends Standard
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const STATUS_SOLD_OUT = 2;

    public static $statusLabel = [
        self::STATUS_OFFLINE => '下線',
        self::STATUS_ONLINE => '上線'
    ];

    public function rules()
    {
        $rules = parent::rules();
        array_push($rules,
            [['name', 'sn', 'status'], 'required', 'on' => self::SCENARIO_CREATE],
            [['name', 'sn', 'status', 'create_time'], 'required', 'on' => self::SCENARIO_UPDATE]
        );
        return $rules;
    }

    public function getProduct()
    {
        return $this->hasOne(ProductModel::class, ['id' => 'product_id']);
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
                $this->correctParamsNumber();
            }
            return true;
        } else {
            return false;
        }
    }

    private function correctParamsNumber()
    {
        $params = ['stock', 'original_price', 'price'];
        foreach ($params as $p) {
            $this->$p = intval($this->$p);
            if ($this->$p < 0) {
                $this->$p = 0;
            }
        }
    }

    /**
     * @param int $productId
     * @param bool $admin
     * @return static[]
     */
    public static function findByProductId(int $productId, bool $admin = true)
    {
        $params = ['product_id' => $productId];
        if (!$admin) {
            $params['status'] = self::STATUS_ONLINE;
        }
        return self::findAll($params);
    }

    /**
     * @param int $id
     * @return static|false
     * @throws \yii\db\Exception
     */
    public static function findOnline(int $id)
    {
        $sql = "SELECT * FROM standard WHERE id = :id AND status = " . self::STATUS_ONLINE;
        return self::getDb()->createCommand($sql, [":id" => $id])->queryOne(PDO::FETCH_OBJ);
    }

    public static function updateSoldStock(int $id, int $quantity, bool $rollback = false)
    {
        self::getDb()->createCommand("set autocommit = 0")->execute();
        self::getDb()->createCommand("begin work")->execute();
        $stock = self::getDb()->createCommand("SELECT stock FROM standard WHERE id = :id FOR UPDATE",
            [":id" => $id])->queryScalar();
        $success = false;
        if (!$rollback) {
            if (!empty($stock) && $stock >= $quantity) {
                $result = self::getDb()->createCommand("UPDATE standard SET stock = stock - :quantity, sold_quantity = sold_quantity + :quantity WHERE id = :id",
                    [":quantity" => $quantity, ":id" => $id])->execute();
                if (!empty($result)) {
                    $success = true;
                }
            }
        } else { // 庫存回補
            $result = self::getDb()->createCommand("UPDATE standard SET stock = stock + :quantity, sold_quantity = sold_quantity - :quantity WHERE id = :id",
                [":quantity" => $quantity, ":id" => $id])->execute();
            if (!empty($result)) {
                $success = true;
            }
        }
        self::getDb()->createCommand("commit work")->execute();
        self::getDb()->createCommand("set autocommit = 1")->execute();
        return $success;
    }
}