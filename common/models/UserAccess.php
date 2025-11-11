<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_access".
 *
 * @property int $id
 * @property int $access_id
 * @property int $user_id
 * @property string $create_time
 */
class UserAccess extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_access';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['access_id', 'user_id', 'create_time'], 'required'],
            [['access_id', 'user_id'], 'integer'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'access_id' => 'Access ID',
            'user_id' => 'User ID',
            'create_time' => 'Create Time',
        ];
    }
}
