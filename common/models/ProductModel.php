<?php

namespace common\models;


use ball\helper\File;
use PDO;
use yii\db\Expression;

class ProductModel extends Product
{
    use MediaTrait;
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;

    const DEADLINE_OFF = 0;
    const DEADLINE_ON = 1;

    const STANDARD_TYPE_SINGLE = 0;
    const STANDARD_TYPE_DOUBLE = 1;

    public static $statusLabel = [
        self::STATUS_OFFLINE => '下線',
        self::STATUS_ONLINE => '上線'
    ];

    public static $deadlineLabel = [
        self::DEADLINE_OFF => '永遠',
        self::DEADLINE_ON => '限時'
    ];

    public static $standardTypeLabel = [
        self::STANDARD_TYPE_SINGLE => '單規',
        self::STANDARD_TYPE_DOUBLE => '雙規'
    ];

    public function init()
    {
        parent::init();
        $this->mediaAttribute = [
            'media' => [
                'category' => File::CATEGORY_PRODUCT,
                'size' => 1
            ]
        ];
    }

    public function rules()
    {
        $rules = parent::rules();
        array_push($rules,
            [['name', 'sn', 'status', 'owner_id'], 'required', 'on' => self::SCENARIO_CREATE],
            [['name', 'sn', 'status', 'create_time', 'owner_id'], 'required', 'on' => self::SCENARIO_UPDATE]
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
        $sql = "SELECT p.*, u.id AS user_id, u.name AS user_name FROM product p
          LEFT OUTER JOIN user u ON p.owner_id = u.id WHERE 1 = 1 ";
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
        $sql = "SELECT count(*) FROM product WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (name like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (!empty($search['status'])) {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }
        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }

    /**
     * @param int $productId
     * @param int $ownerId
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function isOwnerProduct(int $productId, int $ownerId)
    {
        $sql = "SELECT 1 FROM product WHERE owner_id = :owner_id AND id = :id";
        $result = self::getDb()->createCommand($sql, [":id" => $productId, ":owner_id" => $ownerId])->queryScalar();
        return $result == 1 ? true : false;
    }

    public static function findByStatus(int $productId, int $ownerId)
    {
        $sql = "SELECT 1 FROM product WHERE owner_id = :owner_id AND id = :id";
        $result = self::getDb()->createCommand($sql, [":id" => $productId, ":owner_id" => $ownerId])->queryScalar();
        return $result == 1 ? true : false;
    }

    /**
     * @param int $productId
     * @return static|false
     * @throws \yii\db\Exception
     */
    public static function findOnline(int $productId)
    {
        $sql = "SELECT * FROM product WHERE id = :id AND status = " . self::STATUS_ONLINE . "
            AND (deadline = " . self::DEADLINE_OFF . " OR
            (deadline = " . self::DEADLINE_ON . " AND now() BETWEEN start_time AND end_time))";
        return self::getDb()->createCommand($sql, [":id" => $productId])->queryOne(PDO::FETCH_OBJ);
    }

    public static function findOnlineByStandard(int $standardId)
    {
        $sql = "SELECT p.* FROM product p INNER JOIN standard s ON p.id = s.product_id
            WHERE s.id = :id AND p.status = " . self::STATUS_ONLINE . "  AND s.status = " . StandardModel::STATUS_ONLINE . "
            AND (p.deadline = " . self::DEADLINE_OFF . " OR
            (p.deadline = " . self::DEADLINE_ON . " AND now() BETWEEN p.start_time AND p.end_time))";
        return self::getDb()->createCommand($sql, [":id" => $standardId])->queryOne(PDO::FETCH_OBJ);
    }
}
