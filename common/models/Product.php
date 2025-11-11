<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int $status
 * @property int $standard_type 0: 單規, 1: 雙規
 * @property string $name
 * @property string $sn 產品編號
 * @property int $owner_id
 * @property string $views
 * @property string $clicks
 * @property int $deadline
 * @property string $start_time
 * @property string $end_time
 * @property string $intro
 * @property string $note
 * @property string $media
 * @property int $sort
 * @property string $create_time
 * @property string $update_time
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'owner_id', 'sort'], 'integer'],
            [['name', 'sn', 'create_time'], 'required'],
            [['views', 'clicks'], 'number'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
            [['intro', 'note', 'media'], 'string'],
            [['standard_type', 'deadline'], 'string', 'max' => 4],
            [['name'], 'string', 'max' => 256],
            [['sn'], 'string', 'max' => 128],
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
            'standard_type' => 'Standard Type',
            'name' => 'Name',
            'sn' => 'Sn',
            'owner_id' => 'Owner ID',
            'views' => 'Views',
            'clicks' => 'Clicks',
            'deadline' => 'Deadline',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'intro' => 'Intro',
            'note' => 'Note',
            'media' => 'Media',
            'sort' => 'Sort',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
