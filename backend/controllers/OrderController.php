<?php

namespace backend\controllers;


use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use ball\util\Url;
use common\models\OrdersModel;
use Yii;
use yii\helpers\Html;

class OrderController extends BackendController
{
    protected $actionLabel = '訂單';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword', 'type']);
        $list = OrdersModel::search($search, Pagination::PAGE_SIZE, $start);
        $count = OrdersModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }

    public function actionCreate()
    {
        $model = new OrdersModel([
            'scenario' => OrdersModel::SCENARIO_CREATE
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('建立成功');
            }
            return $this->redirect(Url::to("index"));
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    public function actionUpdate()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = OrdersModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('更新成功');
            }
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->render('update', compact('model'));
        }
    }

    public function actionDelete()
    {
        $ids = Yii::$app->request->get('id');
        if(empty($ids)) {
            return $this->redirect(Url::to(["index", "id" => null]));
        }
        $ids = explode(",", $ids);
        foreach($ids as $id) {
            OrdersModel::deleteAll(['id' => $id]);
        }
        HtmlHelper::setMessage('刪除成功');
        return $this->redirect(Url::to(["index", "id" => null]));
    }
}