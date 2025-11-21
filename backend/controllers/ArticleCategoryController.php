<?php

namespace backend\controllers;


use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\ArticleCategoryModel;
use Yii;
use yii\helpers\Html;

class ArticleCategoryController extends BackendController
{
    protected $actionLabel = '文章分類';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword']);
        $list = ArticleCategoryModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = ArticleCategoryModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }

    public function actionCreate()
    {
        $model = new ArticleCategoryModel(['scenario' => ArticleCategoryModel::SCENARIO_CREATE]);
        if ($model->load(Yii::$app->request->post())) {
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
        $id = intval(Yii::$app->request->get('id'));
        $model = ArticleCategoryModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $model->scenario = ArticleCategoryModel::SCENARIO_UPDATE;
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
        ArticleCategoryModel::deleteAll(['id' => $id]);
        HtmlHelper::setMessage('刪除成功');
        return $this->redirect(['index' . $this->queryString]);
    }
}