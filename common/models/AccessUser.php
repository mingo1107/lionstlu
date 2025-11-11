<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "access_user".
 *
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property string $access_list
 * @property string $create_time
 * @property string $update_time
 */
class AccessUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id'], 'integer'],
            [['access_list'], 'string'],
            [['create_time'], 'required'],
            [['create_time', 'update_time'], 'safe'],
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
            'role_id' => 'Role ID',
            'access_list' => 'Access List',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
