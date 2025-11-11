<?php

namespace common\models;


use PDO;
use Yii;
use yii\db\Expression;

class CooperateModel extends Cooperate
{
    const SCENARIO_CREATE = 'create';

    //客服狀態 0: 未處理 1: 已處理 2: 等待客戶回應 3: 等待客服回應
    const STATUS_UNHANDLED = 0;
    const STATUS_HANDLED = 1;
    const STATUS_PENDING_CUSTOMER_RESPONSE = 2;
    const STATUS_PENDING_SERVICE_RESPONSE = 3;

    public static $statusLabel = [
        self::STATUS_UNHANDLED => '未處理',
        self::STATUS_HANDLED => '已處理',
        self::STATUS_PENDING_CUSTOMER_RESPONSE => '等待客戶回應',
        self::STATUS_PENDING_SERVICE_RESPONSE => '等待客服回應',
    ];

    public function rules()
    {
        $rules = parent::rules();
        array_push($rules,
            [['name', 'email', 'title', 'content'], 'required', 'on' => self::SCENARIO_CREATE]
        );
        return $rules;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->scenario == self::SCENARIO_CREATE) {
                if (yii::$app->user->isGuest) {
                    $this->member_id = 0;
                } else {
                    $this->member_id = yii::$app->user->getId();
                }
                $this->status = self::STATUS_UNHANDLED;
                $this->create_time = new Expression('now()');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $search
     * @param int|null $limit
     * @param int|null $offset
     * @return static[]
     * @throws \yii\db\Exception
     */
    public static function query(array $search, int $limit = null, int $offset = null)
    {
        $sql = "SELECT * FROM cooperate WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (title like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (isset($search['status'])) {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }

        if (isset($search['category'])) {
            $sql .= " and category = :category";
            $params[":category"] = $search['category'];
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
        $sql = "SELECT count(*) FROM cooperate WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and title like :keyword";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (isset($search['status'])) {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }

        if (isset($search['category'])) {
            $sql .= " and category = :category";
            $params[":category"] = $search['category'];
        }
        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }
}