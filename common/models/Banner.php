<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "banner".
 *
 * @property int $id
 * @property int $status
 * @property int $type
 * @property string $name
 * @property string $link
 * @property string $media
 * @property string $media_m
 * @property int $sort
 * @property string $create_time
 * @property string $update_time
 */
class Banner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'banner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'type', 'sort'], 'integer'],
            [['name', 'link', 'media', 'media_m', 'create_time', 'update_time'], 'required'],
            [['media', 'media_m'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['link'], 'string', 'max' => 256],
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
            'type' => 'Type',
            'name' => 'Name',
            'link' => 'Link',
            'media' => 'Media',
            'media_m' => 'Media M',
            'sort' => 'Sort',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
