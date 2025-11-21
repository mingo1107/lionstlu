<?php

namespace backend\controllers;

use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\BannerModel;
use Yii;
use yii\helpers\Html;

class SlideController extends BackendController
{
    protected $actionLabel = '首頁投影片';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword']);
        $search['type'] = BannerModel::TYPE_SLIDE;
        $list = BannerModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = BannerModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }

    public function actionCreate()
    {
        $model = new BannerModel([
            'scenario' => BannerModel::SCENARIO_CREATE
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $model->type = BannerModel::TYPE_SLIDE;
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
        $model = BannerModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $model->scenario = BannerModel::SCENARIO_UPDATE;
        $media = $model->serializeMedia('media');
        $mediaM = $model->serializeMedia('media_m');
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('更新成功');
            }
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->render('update', ['model' => $model, 'media' => $media, 'mediaM' => $mediaM]);
        }
    }

    public function actionDelete()
    {
        $id = intval(Yii::$app->request->get('id'));
        BannerModel::deleteAll(['id' => $id]);
        HtmlHelper::setMessage('刪除成功');
        return $this->redirect(['index' . $this->queryString]);
    }
}