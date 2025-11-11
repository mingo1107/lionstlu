<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_service".
 *
 * @property int $id
 * @property int $status
 * @property int $category
 * @property string $title
 * @property string $content
 * @property int $member_id
 * @property string $create_time
 * @property string $name
 * @property string $email
 * @property string $mobile
 */
class CustomerService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'category', 'member_id'], 'integer'],
            [['title', 'content', 'create_time', 'name', 'email'], 'required'],
            [['content'], 'string'],
            [['create_time'], 'safe'],
            [['title', 'name'], 'string', 'max' => 128],
            [['email'], 'string', 'max' => 256],
            [['mobile'], 'string', 'max' => 64],
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
            'category' => 'Category',
            'title' => 'Title',
            'content' => 'Content',
            'member_id' => 'Member ID',
            'create_time' => 'Create Time',
            'name' => 'Name',
            'email' => 'Email',
            'mobile' => 'Mobile',
        ];
    }
}
