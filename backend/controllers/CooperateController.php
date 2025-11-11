<?php

namespace backend\controllers;


use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\CooperateModel;

class CooperateController extends BackendController
{
    protected $actionLabel = '合作提案';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword']);
        $list = CooperateModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = CooperateModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }
}