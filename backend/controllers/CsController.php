<?php

namespace backend\controllers;


use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\CustomerServiceModel;
use Yii;
use yii\helpers\Html;

class CsController extends BackendController
{
    protected $actionLabel = '客服';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['category']);
        $list = CustomerServiceModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = CustomerServiceModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }
    
    public function actionUpdate()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = CustomerServiceModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
//        $model->scenario = CustomerServiceModel::SCENARIO_UPDATE;
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
        $id = intval(Yii::$app->request->get('id'));
        CustomerServiceModel::deleteAll(['id' => $id]);
        HtmlHelper::setMessage('刪除成功');
        return $this->redirect(['index' . $this->queryString]);
    }
}