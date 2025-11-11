<?php

namespace common\models;


use PDO;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class AccessRoleModel extends AccessRole
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new Expression('NOW()'),
            ]
        ]);
    }

    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            static::SCENARIO_CREATE => ['name', 'access_list'],
            static::SCENARIO_UPDATE => ['name', 'access_list']
        ]);
    }

    /*
        MANAGER     管理員
        USER        一般使用者
        COLUMNIST   專欄作家
    */
    public function roleAuthority($roleId)
    {
        switch($roleId){
            case 1:
            case 6:
                $roleAuthority = 'MANAGER';
                break;
            case 7:
                $roleAuthority = 'COLUMNIST';
                break;
            default:
                $roleAuthority = 'USER';
                break;
        }

        return $roleAuthority;
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
        $sql = "SELECT * FROM access_role WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (name like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
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
        $sql = "SELECT count(*) FROM access_role WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and name like :keyword";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }
}
