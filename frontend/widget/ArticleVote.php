<?php

namespace frontend\widget;


use common\models\ArticleModel;
use common\models\VoteModel;
use common\models\VoteOptionModel;
use yii\base\Widget;

class ArticleVote extends Widget
{
    /**
     * @var ArticleModel
     */
    public $article;

    public function run()
    {
        $vote = VoteModel::findOnline($this->article->ad_id);
        $optionList = [];
        if (!empty($vote)) {
            $optionList = VoteOptionModel::findAllByVoteId($vote->id);
        }
        return $this->render('article-vote', ['vote' => $vote, 'optionList' => $optionList]);
    }
}