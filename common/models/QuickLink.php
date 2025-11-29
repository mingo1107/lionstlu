<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "quick_link".
 *
 * @property int $id
 * @property string $title 標題
 * @property string $url 連結
 * @property string $icon 圖示
 * @property int $sort 排序
 * @property int $is_login 是否需登入 0:否 1:是
 * @property int $status 狀態 0:下線 1:上線
 * @property string $create_time
 * @property string $update_time
 */
class QuickLink extends ActiveRecord
{
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;

    const IS_LOGIN_NO = 0;
    const IS_LOGIN_YES = 1;

    public static $isLoginLabel = [
        self::IS_LOGIN_NO => '不限',
        self::IS_LOGIN_YES => '需登入',
    ];

    public static $statusLabel = [
        self::STATUS_OFFLINE => '下線',
        self::STATUS_ONLINE => '上線',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quick_link';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_time', 'update_time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_time'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'url'], 'required'],
            [['sort', 'is_login', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['title'], 'string', 'max' => 64],
            [['url', 'icon'], 'string', 'max' => 255],
            [['sort'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_ONLINE],
            [['is_login'], 'default', 'value' => self::IS_LOGIN_NO],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '標題',
            'url' => '連結',
            'icon' => '圖示',
            'sort' => '排序',
            'is_login' => '權限限制',
            'status' => '狀態',
            'create_time' => '建立時間',
            'update_time' => '更新時間',
        ];
    }

    /**
     * 取得前台顯示用的連結列表
     * @return QuickLink[]
     */
    public static function getFrontendLinks()
    {
        return self::find()
            ->where(['status' => self::STATUS_ONLINE])
            ->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])
            ->all();
    }
}
