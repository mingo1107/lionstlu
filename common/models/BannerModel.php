<?php

namespace common\models;


use ball\helper\File;
use PDO;
use yii\db\Expression;

class BannerModel extends Banner
{
    use MediaTrait;
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;

    const TYPE_BANNER = 0;
    const TYPE_SLIDE = 1;

    public static $statusLabel = [
        self::STATUS_ONLINE => '上線',
        self::STATUS_OFFLINE => '下線'
    ];

    public static $typeLabel = [
        self::TYPE_BANNER => '一般圖片',
        self::TYPE_SLIDE => '首頁投影片'
    ];

    public function init()
    {
        parent::init();
        $this->mediaAttribute = [
            'media' => [
                'category' => File::CATEGORY_BANNER,
                'size' => 1
            ],
            'media_m' => [
                'size' => 1,
                'category' => File::CATEGORY_BANNER,
            ]
        ];
    }

    public function rules()
    {
        $rules = parent::rules();
        array_push($rules,
            [['name', 'media', 'status', 'sort', 'link'], 'required', 'on' => self::SCENARIO_CREATE],
            [['name', 'media', 'status', 'sort', 'link'], 'required', 'on' => self::SCENARIO_UPDATE]
        );
        return $rules;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->scenario == self::SCENARIO_CREATE || $this->scenario == self::SCENARIO_UPDATE) {
                $this->update_time = new Expression('now()');
                if ($this->scenario == self::SCENARIO_CREATE) {
                    $this->create_time = new Expression('now()');
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
        $sql = "SELECT * FROM banner WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (name like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if ($search['status'] !== '') {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }

        if ($search['type'] !== '') {
            $sql .= " and type = :type";
            $params[":type"] = $search['type'];
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
        $sql = "SELECT count(*) FROM banner WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and name like :keyword";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (!empty($search['status'])) {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }

        if ($search['type'] !== '') {
            $sql .= " and type = :type";
            $params[":type"] = $search['type'];
        }

        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }

    /**
     * @param int $type
     * @param int $status
     * @param int $limit
     * @return array
     * @throws \yii\db\Exception
     */
    public static function findByTypeAndStatus(int $type, int $status, int $limit = 8)
    {
        $sql = "SELECT * FROM banner WHERE status = :status and type = :type ORDER BY sort DESC limit $limit";
        return self::getDb()->createCommand($sql, [":status" => $status, ":type" => $type])->queryAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $type
     * @param int $limit
     * @return array
     * @throws \yii\db\Exception
     */
    public static function findAllRandomByType(int $type, int $limit)
    {
        $sql = "SELECT r1.*
             FROM banner AS r1 JOIN
                   (SELECT CEIL(RAND() * (SELECT MAX(id) FROM banner)) AS id)
                    AS r2
             WHERE r1.id >= r2.id and r1.status = " . self::STATUS_ONLINE . " and r1.type = :type
             ORDER BY r1.id ASC
             LIMIT $limit";
        return self::getDb()->createCommand($sql, [":type" => $type])->queryAll(PDO::FETCH_OBJ);
    }
}