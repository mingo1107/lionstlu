<?php

namespace backend\controllers;


use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\AreaModel;
use common\models\MemberModel;
use Yii;
use yii\helpers\Html;

class MemberController extends BackendController
{
    protected $actionLabel = '會員';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword', 'area_id', 'is_self_register']);
        $list = MemberModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = MemberModel::count($search);
        $areaList = AreaModel::findAllForSelect();
        return $this->render('index', [
            'list' => $list,
            'start' => $start,
            'count' => $count,
            'areaList' => $areaList,
            'search' => $search
        ]);
    }

    public function actionCreate()
    {
        $model = new MemberModel(['scenario' => MemberModel::SCENARIO_CREATE]);
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('建立成功');
            }
            return $this->redirect(['option?id=' . $model->id]);
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    public function actionUpdate()
    {
        $id = intval(yii::$app->request->get('id'));
        $model = MemberModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $model->scenario = MemberModel::SCENARIO_UPDATE;
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('更新成功');
            }
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }
}