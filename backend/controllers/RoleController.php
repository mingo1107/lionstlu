<?php

namespace backend\controllers;


use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\AccessModel;
use common\models\AccessRoleModel;
use Yii;
use yii\helpers\Html;

class RoleController extends BackendController
{
    protected $actionLabel = '權限群組';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword']);
        $list = AccessRoleModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = AccessRoleModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }

    public function actionCreate()
    {
        $model = new AccessRoleModel(['scenario' => AccessRoleModel::SCENARIO_CREATE]);
        $availableAccessIdList = AccessModel::findIdListByVisible();
        if ($model->load(Yii::$app->request->post())) {
            $list = yii::$app->request->post('access');
            if (!empty($list)) {
                $accessList = [];
                foreach ($list as $accessId) {
                    if (in_array($accessId, $availableAccessIdList)) {
                        $accessList[$accessId] = ["*"];
                    }
                }
                $model->access_list = json_encode($accessList);
                $model->save();
                if (!empty($model->errors)) {
                    HtmlHelper::setError(Html::errorSummary($model));
                } else {
                    HtmlHelper::setMessage('新增成功');
                }
            } else {
                HtmlHelper::setError('新增失敗，請至少勾選一個權限');
            }
            return $this->redirect(['index' . $this->queryString]);
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    public function actionUpdate()
    {
        $id = intval(yii::$app->request->get('id'));
        $model = AccessRoleModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $model->scenario = AccessRoleModel::SCENARIO_UPDATE;
        $availableAccessIdList = AccessModel::findIdListByVisible();
        if ($model->load(Yii::$app->request->post())) {
            $list = yii::$app->request->post('access');
            if (!empty($list)) {
                $accessList = [];
                foreach ($list as $accessId) {
                    if (in_array($accessId, $availableAccessIdList)) {
                        $accessList[$accessId] = ["*"];
                    }
                }
                $model->access_list = json_encode($accessList);
                $model->save();
                if (!empty($model->errors)) {
                    HtmlHelper::setError(Html::errorSummary($model));
                } else {
                    HtmlHelper::setMessage('更新成功');
                }
            } else {
                HtmlHelper::setError('請至少勾選一個權限');
            }
            return $this->redirect(['update' . $this->queryString]);
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    public function actionDelete()
    {
        $id = intval(yii::$app->request->get('id'));
        $model = AccessRoleModel::findOne(["id" => $id]);
        if (!empty($model)) {
            $model->delete();
            HtmlHelper::setMessage('刪除成功');
        }
        return $this->redirect('index');
    }
}