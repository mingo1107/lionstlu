<?php

namespace common\models;


use PDO;
use yii\db\Expression;

class ArticleCategoryModel extends ArticleCategory
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;

    public static $statusLabel = [
        self::STATUS_ONLINE => '上線',
        self::STATUS_OFFLINE => '下線'
    ];

    public function rules()
    {
        return [
            [['name', 'create_time'], 'required'],
            [['sort'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['name', 'status', 'sort'], 'required', 'on' => self::SCENARIO_CREATE],
            [['name', 'status', 'sort'], 'required', 'on' => self::SCENARIO_UPDATE]
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
        $sql = "SELECT * FROM article_category WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (name like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if ($search['status'] !== '') {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
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
        $sql = "SELECT count(*) FROM article_category WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and name like :keyword";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (!empty($search['status'])) {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }
        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }

    /**
     * @param int $status
     * @return static[]
     * @throws \yii\db\Exception
     */
    public static function findByStatus(int $status)
    {
        $sql = "SELECT * FROM article_category WHERE status = :status ORDER BY sort DESC";
        return self::getDb()->createCommand($sql, [":status" => $status])->queryAll(PDO::FETCH_OBJ);
    }
}