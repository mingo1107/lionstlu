<?php

namespace frontend\controllers;


use ball\helper\HtmlHelper;
use common\models\CooperateModel;
use common\models\CustomerServiceModel;
use Yii;
use yii\helpers\Html;

class CsController extends FrontendController
{
    public function actionIndex()
    {

        $model = new CustomerServiceModel(['scenario' => CustomerServiceModel::SCENARIO_CREATE]);
        if ($model->load(yii::$app->request->post())) {
            if ($model->save()) {
                HtmlHelper::setMessage('您的請求已經成功送出，客服人員稍後會主動與您聯繫，<a href="/">點此回首頁</a>');
                return $this->redirect(yii::$app->request->referrer);
            } else {
                HtmlHelper::setError(Html::errorSummary($model));
            }
        }
        return $this->render('index', ['model' => $model]);
    }

    public function actionCooperate()
    {
        $breadcrumbs = [
            ['label' => '合作提案']
        ];
        $model = new CooperateModel(['scenario' => CooperateModel::SCENARIO_CREATE]);
        if ($model->load(yii::$app->request->post())) {
            if ($model->save()) {
                HtmlHelper::setMessage('您的合作提案已經成功送出，客服人員稍後會主動與您聯繫，<a href="/">點此回首頁</a>');
                return $this->redirect(yii::$app->request->referrer);
            } else {
                HtmlHelper::setError(Html::errorSummary($model));
            }
        }
        return $this->render('cooperate', ['breadcrumbs' => $breadcrumbs, 'model' => $model]);
    }
}