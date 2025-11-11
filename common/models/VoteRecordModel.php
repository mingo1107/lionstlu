<?php

namespace common\models;

use ball\helper\File;
use PDO;
use yii\db\Expression;


class VoteRecordModel extends VoteRecord
{
    /**
     * @param int $productId
     * @return static|false
     * @throws \yii\db\Exception
     */
    public static function findByVoteAndMemberId(int $voteId, int $memberId)
    {
        $sql = "SELECT *
                FROM vote_record
                WHERE vote_id = :vote_id
                    AND member_id = :member_id ";
        return self::getDb()->createCommand($sql, [":vote_id" => $voteId, ":member_id" => $memberId])->queryOne(PDO::FETCH_OBJ);
    }


    /**
     * @param int $productId
     * @return static|false
     * @throws \yii\db\Exception
     */
    public static function findByVoteAndDateAndMemberId(int $voteId, int $memberId, string $date)
    {
        $sql = "SELECT *
                FROM vote_record
                WHERE vote_id = :vote_id
                    AND member_id = :member_id
                    AND create_time like '".$date." %' ";
        return self::getDb()->createCommand($sql, [":vote_id" => $voteId, ":member_id" => $memberId])->queryOne(PDO::FETCH_OBJ);
    }
}
