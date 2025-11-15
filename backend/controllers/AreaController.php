<?php

namespace backend\controllers;

use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\AreaModel;
use Yii;
use yii\helpers\Html;

class AreaController extends BackendController
{
    protected $actionLabel = '區域';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['keyword', 'user_id']);
        $list = AreaModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = AreaModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }

    public function actionCreate()
    {
        $model = new AreaModel(['scenario' => AreaModel::SCENARIO_CREATE]);
        if ($model->load(Yii::$app->request->post())) {
            // 自動設定建立者為當前登入的後台使用者
            $model->user_id = Yii::$app->user->getId();
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('建立成功');
            }
            return $this->redirect(['index' . $this->queryString]);
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    public function actionUpdate()
    {
        $id = intval(yii::$app->request->get('id'));
        $model = AreaModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $model->scenario = AreaModel::SCENARIO_UPDATE;
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

    public function actionDelete()
    {
        $id = intval(yii::$app->request->get('id'));
        AreaModel::deleteAll(['id' => $id]);
        HtmlHelper::setMessage('刪除成功');
        return $this->redirect(['index' . $this->queryString]);
    }
}

