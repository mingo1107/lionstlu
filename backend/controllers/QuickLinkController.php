<?php

namespace backend\controllers;

use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\QuickLink;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class QuickLinkController extends BackendController
{
    protected $actionLabel = '快速連結';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = [];

        // 處理狀態篩選
        $status = Yii::$app->request->get('status');
        if ($status !== null && $status !== '') {
            $search['status'] = $status;
        }

        // 處理權限篩選
        $isLogin = Yii::$app->request->get('is_login');
        if ($isLogin !== null && $isLogin !== '') {
            $search['is_login'] = $isLogin;
        }

        // 處理關鍵字搜尋 (搜尋標題欄位)
        $keyword = Yii::$app->request->get('keyword');
        if (!empty($keyword)) {
            $search[] = ['like', 'title', $keyword];
        }

        $list = QuickLink::find()
            ->where($search)
            ->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])
            ->limit(Pagination::PAGE_SIZE)
            ->offset($start)
            ->all();
        $count = QuickLink::find()->where($search)->count();

        return $this->render('index', [
            'list' => $list,
            'start' => $start,
            'count' => $count
        ]);
    }

    public function actionCreate()
    {
        $model = new QuickLink();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                HtmlHelper::setMessage('建立成功');
                return $this->redirect(['index' . $this->queryString]);
            } else {
                HtmlHelper::setError(Html::errorSummary($model));
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = QuickLink::findOne(['id' => $id]);

        if (empty($model)) {
            HtmlHelper::setError('資料不存在');
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                HtmlHelper::setMessage('更新成功');
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                HtmlHelper::setError(Html::errorSummary($model));
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete()
    {
        return ;
        // $id = intval(Yii::$app->request->get('id'));
        // QuickLink::deleteAll(['id' => $id]);
        // HtmlHelper::setMessage('刪除成功');
        // return $this->redirect(['index' . $this->queryString]);
    }
}
