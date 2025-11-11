<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders_daily_count".
 *
 * @property int $id
 * @property int $count
 * @property string $date
 * @property string $create_time
 * @property string $update_time
 */
class OrdersDailyCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_daily_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'create_time'], 'required'],
            [['id', 'count'], 'integer'],
            [['date', 'create_time', 'update_time'], 'safe'],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'count' => 'Count',
            'date' => 'Date',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
