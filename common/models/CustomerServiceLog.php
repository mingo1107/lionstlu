<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_service_log".
 *
 * @property int $id
 * @property int $customer_service_id
 * @property int $role 0: member, 1: admin
 * @property string $content
 * @property int $owner_id id of member or user
 * @property string $create_time
 */
class CustomerServiceLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_service_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_service_id', 'owner_id'], 'integer'],
            [['content', 'create_time'], 'required'],
            [['content'], 'string'],
            [['create_time'], 'safe'],
            [['role'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_service_id' => 'Customer Service ID',
            'role' => 'Role',
            'content' => 'Content',
            'owner_id' => 'Owner ID',
            'create_time' => 'Create Time',
        ];
    }
}
