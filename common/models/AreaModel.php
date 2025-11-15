<?php

namespace common\models;

use PDO;
use yii\db\Expression;

class AreaModel extends Area
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public function rules()
    {
        return [
            [['area_name', 'create_time'], 'required'],
            [['sort', 'user_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['area_name'], 'string', 'max' => 128],
            [['area_name', 'sort'], 'required', 'on' => self::SCENARIO_CREATE],
            [['area_name', 'sort'], 'required', 'on' => self::SCENARIO_UPDATE]
        ];
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

    /**
     * @param array $search
     * @param int|null $limit
     * @param int|null $offset
     * @return static[]
     * @throws \yii\db\Exception
     */
    public static function query(array $search, int $limit = null, int $offset = null)
    {
        $sql = "SELECT * FROM area WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (area_name like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (!empty($search['user_id'])) {
            $sql .= " and user_id = :user_id";
            $params[":user_id"] = $search['user_id'];
        }

        $sql .= " order by sort desc ";
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
        $sql = "SELECT count(*) FROM area WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and area_name like :keyword";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (!empty($search['user_id'])) {
            $sql .= " and user_id = :user_id";
            $params[":user_id"] = $search['user_id'];
        }

        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }

    /**
     * 取得所有區域列表（用於下拉選單）
     * @return static[]
     * @throws \yii\db\Exception
     */
    public static function findAllForSelect()
    {
        $sql = "SELECT id, area_name FROM area ORDER BY sort DESC, id ASC";
        return self::getDb()->createCommand($sql)->queryAll(PDO::FETCH_OBJ);
    }
}

