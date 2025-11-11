<?php

namespace common\models;


use PDO;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class AccessModel extends Access
{
    const STATUS_OFFLINE = 0;
    const STATUS_VISIBLE = 1;
    const STATUS_INVISIBLE = 2;
    const STATUS_SUPER = 3;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => null,
                'value' => new Expression('NOW()'),
            ]
        ]);
    }

    public static function findByPatternAndStatus(string $pattern, int $status)
    {
        return static::findOne(['pattern' => $pattern, 'status' => $status]);
    }

    /**
     * @param string $pattern
     * @return static|false
     * @throws \yii\db\Exception
     */
    public static function findOnlineByPattern(string $pattern)
    {
        return self::getDb()->createCommand("SELECT * FROM access WHERE status IN (:visible, :invisible) AND pattern = :pattern",
            [":visible" => static::STATUS_VISIBLE, ":invisible" => static::STATUS_INVISIBLE, ":pattern" => $pattern])->queryOne(PDO::FETCH_OBJ);
    }

    /**
     * @return static[]
     * @throws \yii\db\Exception
     */
    public static function findAllVisibleParent()
    {
        return self::getDb()->createCommand("SELECT * FROM access WHERE status = :status AND parent_id = 0 ORDER BY sort DESC, id DESC",
            [":status" => static::STATUS_VISIBLE])->queryAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $parentSn
     * @return static[]
     * @throws \yii\db\Exception
     */
    public static function findAllVisibleByParent(int $parentSn)
    {
        return self::getDb()->createCommand("SELECT * FROM access WHERE status = :status AND parent_id = :parent_id ORDER BY sort DESC, id DESC",
            [":status" => static::STATUS_VISIBLE, ":parent_id" => $parentSn])->queryAll(PDO::FETCH_OBJ);
    }

    public static function findAllVisible()
    {
        return self::getDb()->createCommand("SELECT * FROM access WHERE status = :status ORDER BY sort DESC, id DESC",
            [":status" => static::STATUS_VISIBLE])->queryAll(PDO::FETCH_OBJ);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public static function findIdListByVisible()
    {
        $result = [];
        $accessList = self::getDb()->createCommand("SELECT id FROM access WHERE status = :status ORDER BY sort DESC, id DESC",
            [":status" => static::STATUS_VISIBLE])->queryAll(PDO::FETCH_OBJ);
        foreach ($accessList as $access) {
            array_push($result, $access->id);
        }
        return $result;
    }
}