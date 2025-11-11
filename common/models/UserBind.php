<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_bind".
 *
 * @property int $id
 * @property int $user_id
 * @property string $platform sns platform
 * @property string $platform_uid uid of sns platform
 * @property int $status 0: disabled, 1: enabled
 * @property string $create_time
 * @property string $update_time
 */
class UserBind extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_bind';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'integer'],
            [['platform', 'platform_uid', 'create_time'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['platform'], 'string', 'max' => 16],
            [['platform_uid'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'platform' => 'Platform',
            'platform_uid' => 'Platform Uid',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
