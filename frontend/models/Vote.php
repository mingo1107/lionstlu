<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vote".
 *
 * @property int $id
 * @property string $name
 * @property int $owner_id
 * @property int $status
 * @property string $views
 * @property int $deadline
 * @property string $start_time
 * @property string $end_time
 * @property int $sort
 * @property string $create_time
 * @property string $update_time
 */
class Vote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'create_time'], 'required'],
            [['owner_id', 'status', 'deadline', 'sort'], 'integer'],
            [['views'], 'number'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 512],
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
            'owner_id' => 'Owner ID',
            'status' => 'Status',
            'views' => 'Views',
            'deadline' => 'Deadline',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'sort' => 'Sort',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
