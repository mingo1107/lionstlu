<?php

namespace common\models;

use PDO;
use yii\db\Expression;

/**
 * This is the model class for table "orders".
 *
 * @property float $net
 * @property float $gross
 * @property float $payment_fee
 * @property float $shipping_fee
 * @property float $tax
 * @property OrdersDetailModel[] $details
 */
class OrdersModel extends Orders
{
    const SCENARIO_CREATE = 'create';

    public function rules()
    {
        $rules = parent::rules();
        array_push($rules,
            [['no', 'email', 'name', 'mobile', 'country', 'city', 'address', 'zip'], 'required', 'on' => self::SCENARIO_CREATE]
        );
        return $rules;
    }

    public function beforeValidate()
    {
        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->country = '台灣';
            $this->create_time = new Expression('now()');
        }
        return parent::beforeValidate();
    }

    public function getDetails()
    {
        return $this->hasMany(OrdersDetailModel::class, ['orders_id' => 'id']);
    }

    /**
     * @param string $prefix
     * @return string
     * @throws \yii\db\Exception
     */
    public static function generateNo(string $prefix = "O")
    {
        $sql = "SELECT id, count FROM orders_daily_count WHERE date = '" . date("Y-m-d") . "' FOR UPDATE";
        self::getDb()->createCommand("set autocommit = 0")->execute();
        self::getDb()->createCommand("begin work")->execute();
        $dailyCount = self::getDb()->createCommand($sql)->queryOne();
        if (empty($dailyCount)) {
            self::getDb()->createCommand("INSERT INTO orders_daily_count (count, date, create_time) VALUES (1, :date, now())", [":date" => date("Y-m-d")])->execute();
            $counter = 1;
        } else {
            self::getDb()->createCommand("UPDATE orders_daily_count SET count = count + 1, update_time = now() WHERE id = :id", [":id" => $dailyCount['id']])->execute();
            $counter = $dailyCount['count'] + 1;
        }
        self::getDb()->createCommand("commit work")->execute();
        self::getDb()->createCommand("set autocommit = 1")->execute();
        if ($counter < 10000) {
            $orderNo = $prefix . substr(date("Y"), -2) . date("md") . str_pad($counter, 4, 0, STR_PAD_LEFT);
        } else {
            $orderNo = $prefix . substr(date("Y"), -2) . date("md") . $counter;
        }
        return $orderNo;
    }

    /**
     * @param array $search
     * @param int|null $limit
     * @param int|null $offset
     * @return static[]
     * @throws \yii\db\Exception
     */
    public static function search(array $search, int $limit = null, int $offset = null)
    {
        $sql = "SELECT * FROM orders o WHERE 1 = 1 ";
        $params = [];
        if (isset($search['member_id']) && $search['member_id'] !== '') {
            $sql .= " and member_id = :member_id";
            $params[":member_id"] = $search['member_id'];
        }

        if (isset($search['status']) && $search['status'] !== '') {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }

        if (isset($search['no']) && $search['no'] !== '') {
            $sql .= " and no like :no";
            $params[":no"] = "%" . $search['no'] . "%";
        }

        $sql .= " order by id desc ";
        if ($limit !== null) {
            $sql .= " limit $limit";
        }
        if ($offset !== null) {
            $sql .= " offset $offset";
        }
        return self::getDb()->createCommand($sql, $params)->queryAll(PDO::FETCH_OBJ);
    }

    /**
     * @param array $search
     * @return int
     * @throws \yii\db\Exception
     */
    public static function count(array $search = [])
    {
        $sql = "SELECT count(*) FROM orders WHERE 1 = 1 ";
        $params = [];
        if (isset($search['member_id']) && $search['member_id'] !== '') {
            $sql .= " and member_id = :member_id";
            $params[":member_id"] = $search['member_id'];
        }

        if (isset($search['status']) && $search['status'] !== '') {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }

        if (isset($search['no']) && $search['no'] !== '') {
            $sql .= " and no like :no";
            $params[":no"] = "%" . $search['no'] . "%";
        }

        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }
}