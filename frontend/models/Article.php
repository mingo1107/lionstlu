<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property int $category_id
 * @property int $ad_type 0: default, 1: vote, 2: product, 3: external link
 * @property int $target_id
 * @property int $status
 * @property string $link
 * @property string $title
 * @property string $content
 * @property string $cover_media
 * @property string $media
 * @property string $views
 * @property int $deadline
 * @property string $start_time
 * @property string $end_time
 * @property int $sort
 * @property string $create_time
 * @property string $update_time
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'ad_type', 'target_id', 'status', 'sort'], 'integer'],
            [['title', 'content', 'cover_media', 'create_time'], 'required'],
            [['content', 'media'], 'string'],
            [['views'], 'number'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
            [['link', 'cover_media'], 'string', 'max' => 512],
            [['title'], 'string', 'max' => 256],
            [['deadline'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'ad_type' => 'Ad Type',
            'target_id' => 'Target ID',
            'status' => 'Status',
            'link' => 'Link',
            'title' => 'Title',
            'content' => 'Content',
            'cover_media' => 'Cover Media',
            'media' => 'Media',
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
