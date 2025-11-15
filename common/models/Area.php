<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property int $id
 * @property string $area_name
 * @property int $sort
 * @property int $user_id
 * @property string $create_time
 * @property string $update_time
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort', 'user_id'], 'integer'],
            [['area_name', 'create_time'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['area_name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'area_name' => 'Area Name',
            'sort' => 'Sort',
            'user_id' => 'User ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
