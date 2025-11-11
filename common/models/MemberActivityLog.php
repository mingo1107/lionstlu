<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "member_activity_log".
 *
 * @property int $id
 * @property int $user_id
 * @property int $type activity type
 * @property string $ip client's ip
 * @property string $create_time
 */
class MemberActivityLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_activity_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'ip', 'create_time'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['create_time'], 'safe'],
            [['ip'], 'string', 'max' => 32],
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
            'type' => 'Type',
            'ip' => 'Ip',
            'create_time' => 'Create Time',
        ];
    }
}
