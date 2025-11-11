<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vote_option".
 *
 * @property int $id
 * @property int $vote_id
 * @property string $name
 * @property int $count 票數
 * @property string $create_time
 * @property string $update_time
 */
class VoteOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vote_option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vote_id', 'count'], 'integer'],
            [['name', 'create_time'], 'required'],
            [['create_time', 'update_time'], 'safe'],
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
            'vote_id' => 'Vote ID',
            'name' => 'Name',
            'count' => 'Count',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
