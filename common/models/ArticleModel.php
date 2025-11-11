<?php

namespace common\models;


use ball\helper\File;
use PDO;
use yii\db\Expression;

class ArticleModel extends Article
{
    use MediaTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const STATUS_PROMOTE = 2;

    const AD_NONE = 0;
    const AD_VOTE = 1;
    const AD_PRODUCT = 2;
    const AD_LINK = 3;

    const DEADLINE_OFF = 0;
    const DEADLINE_ON = 1;

    public static $statusLabel = [
        self::STATUS_ONLINE => '上線',
        self::STATUS_OFFLINE => '下線',
        self::STATUS_PROMOTE => '焦點',
    ];

    public static $adLabel = [
        self::AD_NONE => '無',
        self::AD_VOTE => '投票',
        self::AD_PRODUCT => '商品',
        self::AD_LINK => '連結'
    ];

    public static $deadlineLabel = [
        self::DEADLINE_OFF => '永遠',
        self::DEADLINE_ON => '限時'
    ];

    private static $sqlOnlineStatus = "status in (" . self::STATUS_ONLINE . ", " . self::STATUS_PROMOTE . ")";

    public function init()
    {
        parent::init();
        $this->mediaAttribute = [
            'media' => ['category' => File::CATEGORY_ARTICLE, 'size' => 8],
            'cover_media' => ['category' => File::CATEGORY_ARTICLE, 'size' => 1]
        ];
    }

    public function rules()
    {
        $rules = parent::rules();
        array_push($rules,
            [['title', 'status', 'sort'], 'required', 'on' => self::SCENARIO_CREATE],
            [['ad_type', 'ad_id', 'og_keywords', 'og_description', 'content_1', 'content_2', 'content_3', 'content_4', 'content_5', 'content_6', 'content_7', 'content_0', 'media', 'cover_media', 'share_count', 'share_location'], 'safe', 'on' => self::SCENARIO_CREATE],
            [['title', 'status', 'sort'], 'required', 'on' => self::SCENARIO_UPDATE],
            [['content_1', 'content_2', 'content_3', 'content_4', 'content_5', 'content_6', 'content_7', 'content_0', 'media', 'cover_media', 'share_count', 'share_location'], 'safe', 'on' => self::SCENARIO_UPDATE]
            
        );
        return $rules;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->scenario == self::SCENARIO_CREATE || $this->scenario == self::SCENARIO_UPDATE) {
                if ($this->scenario == self::SCENARIO_CREATE) {
                    $this->create_time = new Expression('now()');
                    $this->ad_type = self::AD_NONE;
                    $this->ad_id = 0;
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
        $sql = "SELECT a.*, c.name AS category_name FROM article a INNER JOIN article_category c ON a.category_id = c.id WHERE 1 = 1 ";
        $params = [];
        if (isset($search['keyword']) && !empty($search['keyword'])) {
            $sql .= " and (a.title like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (isset($search['ad_type']) && $search['ad_type'] !== '') {
            $sql .= " and a.ad_type = :ad_type";
            $params[":ad_type"] = $search['ad_type'];
        }

        if (isset($search['status']) && $search['status'] !== '') {
            $sql .= " and a.status = :status";
            $params[":status"] = $search['status'];
        }

        if (isset($search['user_id']) && $search['user_id'] !== '') {
            $sql .= " and a.user_id = :user_id";
            $params[":user_id"] = $search['user_id'];
        }

        $sql .= " order by a.sort desc ";
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
        $sql = "SELECT count(*) FROM article WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and title like :keyword";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (isset($search['ad_type']) && $search['ad_type'] !== '') {
            $sql .= " and ad_type = :ad_type";
            $params[":ad_type"] = $search['ad_type'];
        }

        if (!empty($search['status'])) {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }
        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }

    /**
     * @param array $condition
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws \yii\db\Exception
     */
    public static function search(array $condition = [], int $limit = null, int $offset = null)
    {
        $sql = "SELECT * FROM article WHERE " . self::$sqlOnlineStatus;
        $params = [];
        if (isset($condition['keyword']) && trim($condition['keyword']) !== '') {
            $sql .= " and title like :keyword";
            $params[":keyword"] = "%" . trim($condition['keyword']) . "%";
        }

        if (isset($condition['category'])) {
            $sql .= " and category_id = :category_id";
            $params[":category_id"] = intval($condition['category']);
        }

        if (isset($condition['status']) &&
            in_array(intval($condition['status']), [self::STATUS_ONLINE, self::STATUS_PROMOTE])) {
            $sql .= " and status = :status";
            $params[":status"] = intval($condition['status']);
        }

        if (isset($condition['type']) &&
            in_array(intval($condition['type']), [self::AD_PRODUCT, self::AD_VOTE, self::AD_LINK])) {
            $sql .= " and ad_type = :ad_type";
            $params[":ad_type"] = intval($condition['type']);
        }
        $sql .= " order by sort desc, create_time desc ";
        if ($limit !== null) {
            $sql .= " limit $limit";
        }
        if ($offset !== null) {
            $sql .= " offset $offset";
        }
        return self::getDb()->createCommand($sql, $params)->queryAll(PDO::FETCH_OBJ);
    }

    /**
     * @param array $condition
     * @return int
     * @throws \yii\db\Exception
     */
    public static function countSearch(array $condition = [])
    {
        $sql = "SELECT count(*) FROM article WHERE " . self::$sqlOnlineStatus;
        $params = [];
        if (isset($condition['keyword']) && trim($condition['keyword']) !== '') {
            $sql .= " and title like :keyword";
            $params[":keyword"] = "%" . trim($condition['keyword']) . "%";
        }

        if (isset($condition['category'])) {
            $sql .= " and category_id = :category_id";
            $params[":category_id"] = intval($condition['category']);
        }

        if (isset($condition['status']) &&
            in_array(intval($condition['status']), [self::STATUS_ONLINE, self::STATUS_PROMOTE])) {
            $sql .= " and status = :status";
            $params[":status"] = intval($condition['status']);
        }

        if (isset($condition['type']) &&
            in_array(intval($condition['type']), [self::AD_PRODUCT, self::AD_VOTE, self::AD_LINK])) {
            $sql .= " and ad_type = :ad_type";
            $params[":ad_type"] = intval($condition['type']);
        }
        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }

    public static function searchText(array $params = [])
    {
        $text = '';

        if (isset($params['status']) &&
            in_array(intval($params['status']), [self::STATUS_ONLINE, self::STATUS_PROMOTE])) {
            $status = intval($params['status']);
            $text .= "「" . self::$statusLabel[$status] . "文章」";
        }

        if (isset($params['type']) &&
            in_array(intval($params['type']), [self::AD_PRODUCT, self::AD_VOTE, self::AD_LINK])) {
            $type = intval($params['type']);
            $text .= "「包含" . self::$adLabel[$type] . "」";
        }

        if (isset($params['keyword']) && trim($params['keyword']) !== '') {
            $text .= "「{$params['keyword']}」";
        }

        if (empty($text)) {
            $text = '所有文章';
        }
        return $text;
    }

    /**
     * @param int $id
     * @return static|false
     * @throws \yii\db\Exception
     */
    public static function findOneOnlineById(int $id)
    {
        $sql = "SELECT * FROM article WHERE " . self::$sqlOnlineStatus . " AND id = :id";
        return self::getDb()->createCommand($sql, [":id" => $id])->queryOne(PDO::FETCH_OBJ);
    }

    public static function updateViewCount(int $id)
    {
        return self::getDb()->createCommand("UPDATE article SET views = views + 1 WHERE id = :id",
            [":id" => $id])->execute();
    }

    public static function updateShareCount(int $id)
    {
        return self::getDb()->createCommand("UPDATE article SET share_count = share_count + 1 WHERE id = :id",
            [":id" => $id])->execute();
    }

    /**
     * @param int $excludeId
     * @param int $limit
     * @return array
     * @throws \yii\db\Exception
     */
    public static function findAllRandomExcludeId(int $excludeId, int $limit)
    {
//        $sql = "SELECT r1.*
//             FROM article AS r1 JOIN
//                   (SELECT CEIL(RAND() * (SELECT MAX(id) FROM article)) AS id)
//                    AS r2
//             WHERE r1.id >= r2.id and r1.status = " . self::STATUS_ONLINE . " and r1.id <> :id
//             ORDER BY r1.id ASC
//             LIMIT $limit";
        $sql = "SELECT r1.*
             FROM article AS r1
             WHERE  r1.status = " . self::STATUS_ONLINE . " and r1.id <> :id
             ORDER BY rand()
             LIMIT $limit";
        return self::getDb()->createCommand($sql, [":id" => $excludeId])->queryAll(PDO::FETCH_OBJ);
    }
}
