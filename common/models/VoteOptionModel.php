<?php

namespace common\models;


use yii\db\Expression;

class VoteOptionModel extends VoteOption
{
    public static $cssColor = [
        'green', 'pink', 'yellow', 'gray'
    ];

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if(empty($this->create_time)) {
                $this->create_time = new Expression('now()');
            } else {
                $this->update_time = new Expression('now()');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $id
     * @return static[]
     */
    public static function findAllByVoteId(int $id)
    {
        return self::findAll(["vote_id" => $id]);
    }

    /**
     * @param string $name
     * @param int $id
     * @return int
     * @throws \yii\db\Exception
     */
    public static function updateNameById(string $name, int $id)
    {
        return self::getDb()->createCommand("update vote_option set name = :name, update_time = now() 
              where id = :id", [":name" => $name, ":id" => $id])->execute();
    }

    public static function updateCount(int $id)
    {
        return self::getDb()->createCommand("update vote_option set count = count + 1, update_time = now() 
              where id = :id", [":id" => $id])->execute();
    }
}