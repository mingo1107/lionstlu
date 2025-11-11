<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cooperate".
 *
 * @property int $id
 * @property int $status
 * @property string $name
 * @property string $mobile
 * @property string $email
 * @property string $title
 * @property string $content
 * @property int $member_id
 * @property string $create_time
 */
class Cooperate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cooperate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'member_id'], 'integer'],
            [['name', 'email', 'title', 'content', 'create_time'], 'required'],
            [['content'], 'string'],
            [['create_time'], 'safe'],
            [['name', 'title'], 'string', 'max' => 128],
            [['mobile'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'title' => 'Title',
            'content' => 'Content',
            'member_id' => 'Member ID',
            'create_time' => 'Create Time',
        ];
    }
}
