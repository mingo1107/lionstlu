<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property int $category_id
 * @property int $ad_type 0: default, 1: vote, 2: product, 3: external link
 * @property string $ad_link
 * @property int $ad_id
 * @property int $ad_deadline
 * @property string $ad_start_time 廣告開始時間
 * @property string $ad_end_time 廣告結束時間
 * @property int $status
 * @property string $title
 * @property string $content_0
 * @property string $content_1
 * @property string $content_2
 * @property string $content_3
 * @property string $content_4
 * @property string $content_5
 * @property string $content_6
 * @property string $content_7
 * @property string $cover_media
 * @property string $media
 * @property string $views
 * @property int $deadline
 * @property string $start_time
 * @property string $end_time
 * @property string $picsee_link
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
            [['category_id', 'ad_type', 'ad_id', 'ad_deadline', 'status', 'sort'], 'integer'],
            [['ad_start_time', 'ad_end_time', 'start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
            [['title', 'cover_media', 'create_time'], 'required'],
            [['og_keywords', 'og_description', 'content_0', 'content_1', 'content_2', 'content_3', 'content_4', 'content_5', 'content_6', 'content_7', 'media', 'picsee_link'], 'string'],
            [['views'], 'number'],
            [['ad_link', 'cover_media'], 'string', 'max' => 512],
            [['title'], 'string', 'max' => 256],
            //[['deadline'], 'string', 'max' => 4],
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
            'ad_link' => 'Ad Link',
            'ad_id' => 'Ad ID',
            'ad_deadline' => 'Ad Deadline',
            'ad_start_time' => 'Ad Start Time',
            'ad_end_time' => 'Ad End Time',
            'status' => 'Status',
            'title' => 'Title',
            'og_keywords' => 'OG Keywords',
            'og_description' => 'OG Description',
            'content_0' => 'Content 0',
            'content_1' => 'Content 1',
            'content_2' => 'Content 2',
            'content_3' => 'Content 3',
            'content_4' => 'Content 4',
            'content_5' => 'Content 5',
            'content_6' => 'Content 6',
            'content_7' => 'Content 7',
            'cover_media' => 'Cover Media',
            'media' => 'Media',
            'views' => 'Views',
            'deadline' => 'Deadline',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'picsee_link' => 'picsee Link',
            'sort' => 'Sort',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
