<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "access_role".
 *
 * @property int $id
 * @property string $name
 * @property string $access_list list of access ids, JSON format
 * @property string $create_time
 * @property string $update_time
 */
class AccessRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'access_list', 'create_time'], 'required'],
            [['access_list'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'access_list' => 'Access List',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
