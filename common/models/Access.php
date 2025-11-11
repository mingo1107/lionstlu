<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "access".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $pattern
 * @property int $status
 * @property string $name
 * @property string $link
 * @property int $sort
 */
class Access extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'status', 'sort'], 'integer'],
            [['pattern', 'name', 'link'], 'required'],
            [['pattern', 'name'], 'string', 'max' => 64],
            [['link'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'pattern' => 'Pattern',
            'status' => 'Status',
            'name' => 'Name',
            'link' => 'Link',
            'sort' => 'Sort',
        ];
    }
}
