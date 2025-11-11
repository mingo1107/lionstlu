<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vote_record".
 *
 * @property int $id
 * @property int $member_id
 * @property int $vote_id
 * @property int $option_id
 * @property string $ip
 * @property string $create_time
 */
class VoteRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vote_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'vote_id', 'option_id'], 'integer'],
            [['ip', 'create_time'], 'required'],
            [['create_time'], 'safe'],
            [['ip'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'vote_id' => 'Vote ID',
            'option_id' => 'Option ID',
            'ip' => 'Ip',
            'create_time' => 'Create Time',
        ];
    }
}
