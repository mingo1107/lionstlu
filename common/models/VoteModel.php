<?php

namespace common\models;


use PDO;
use yii\db\Expression;

class VoteModel extends Vote
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const STATUS_READONLY = 2;

    const DEADLINE_OFF = 0;
    const DEADLINE_ON = 1;

    const LIMIT_ONCE = 'once';
    const LIMIT_DAILY = 'daily';

    public static $statusLabel = [
        self::STATUS_OFFLINE => '下線',
        self::STATUS_ONLINE => '上線',
        self::STATUS_READONLY => '唯讀'
    ];

    public static $deadlineLabel = [
        self::DEADLINE_OFF => '永遠',
        self::DEADLINE_ON => '限時'
    ];


    public static $voteLimitLabel = [
        self::LIMIT_ONCE => '只能投一次',
        self::LIMIT_DAILY => '一天一次',
    ];

    public function rules()
    {
        return [
            [['name', 'create_time'], 'required'],
            [['owner_id', 'status', 'deadline'], 'integer'],
            [['views'], 'number'],
            [['vote_limit'], 'required'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 512],
            [['name'], 'required', 'on' => self::SCENARIO_CREATE],
            [['name'], 'required', 'on' => self::SCENARIO_UPDATE]
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->scenario == self::SCENARIO_CREATE || $this->scenario == self::SCENARIO_UPDATE) {
                if ($this->scenario == self::SCENARIO_CREATE) {
                    // TODO dynamic owner_id
                    $this->owner_id = 1;
                    $this->create_time = new Expression('now()');
                } else if ($this->scenario == self::SCENARIO_UPDATE) {
                    $this->update_time = new Expression('now()');
                }

                if ($this->deadline == self::DEADLINE_OFF) {
                    $this->start_time = null;
                    $this->end_time = null;
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
        $sql = "SELECT p.*, u.id AS user_id, u.name AS user_name FROM vote p
          LEFT OUTER JOIN user u ON p.owner_id = u.id WHERE 1 = 1  ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (p.name like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if ($search['status'] !== '') {
            $sql .= " and p.status = :status";
            $params[":status"] = $search['status'];
        }

        if (isset($search['user_id']) && $search['user_id'] !== '') {
            $sql .= " and p.user_id = :user_id";
            $params[":user_id"] = $search['user_id'];
        }

        $sql .= " order by p.id desc ";
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
        $sql = "SELECT count(*) FROM vote WHERE 1 = 1 ";
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
     * @param int $id
     * @return static|false
     * @throws \yii\db\Exception
     */
    public static function findOnline(int $id)
    {
        $sql = "SELECT * FROM vote WHERE id = :id AND status = " . self::STATUS_ONLINE . "
            AND (deadline = " . self::DEADLINE_OFF . " OR
            (deadline = " . self::DEADLINE_ON . " AND now() BETWEEN start_time AND end_time))";
        return self::getDb()->createCommand($sql, [":id" => $id])->queryOne(PDO::FETCH_OBJ);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function isOnline(int $id)
    {
        $sql = "SELECT 1 FROM vote WHERE id = :id AND status = " . self::STATUS_ONLINE . "
            AND (deadline = " . self::DEADLINE_OFF . " OR
            (deadline = " . self::DEADLINE_ON . " AND now() BETWEEN start_time AND end_time))";
        return self::getDb()->createCommand($sql, [":id" => $id])->queryScalar() == 1 ? true : false;
    }
}
