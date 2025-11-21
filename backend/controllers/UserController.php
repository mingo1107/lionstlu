<?php

namespace backend\controllers;


use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\AccessModel;
use common\models\AccessRoleModel;
use common\models\AccessUserModel;
use common\models\UserModel;
use Yii;
use yii\helpers\Html;

class UserController extends BackendController
{
    protected $actionLabel = '後台人員';

    public function actionIndex()
    {
        $search = SQLHelper::buildSearchQuery(['status', 'keyword']);
        $start = Pagination::getOffset();
        $list = UserModel::queryAll($search, Pagination::PAGE_SIZE, $start);
        $count = UserModel::count($search);
        return $this->render('index', [
            'start' => $start,
            'count' => $count,
            'list' => $list,
        ]);
    }

    public function actionCreate()
    {
        $model = new UserModel(['scenario' => UserModel::SCENARIO_CREATE]);
        $roleList = AccessRoleModel::find()->all();
        if ($model->load(Yii::$app->request->post())) {
            $checkedUser = UserModel::findOne(['username' => $model->username]);
            if (!empty($checkedUser)) {
                HtmlHelper::setError('已經存在同ID管理員，新增失敗');
                return $this->redirect(['index']);
            }

            $list = Yii::$app->request->post('access');
            $roleId = Yii::$app->request->post('role_id');
            $model->save();
            if (empty($model->errors)) {
                $availableAccessIdList = AccessModel::findIdListByVisible();
                $accessUser = new AccessUserModel(["scenario" => AccessUserModel::SCENARIO_CREATE]);
                if (!empty($list)) {
                    $accessList = [];
                    foreach ($list as $accessId) {
                        if (in_array($accessId, $availableAccessIdList)) {
                            $accessList[$accessId] = ["*"];
                        }
                    }
                    $accessUser->access_list = json_encode($accessList);
                }

                if (!empty($roleId)) {
                    $accessUser->role_id = intval($roleId);
                }
                $accessUser->user_id = $model->id;
                $accessUser->save();
                if (!empty($accessUser->errors)) {
                    HtmlHelper::setError(Html::errorSummary($accessUser));
                }
            }
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('新增成功');
            }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'roleList' => $roleList
        ]);
    }

    public function actionUpdate()
    {

        $id = intval(Yii::$app->request->get('id'));
        $model = UserModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $model->scenario = UserModel::SCENARIO_UPDATE;
        $accessUser = AccessUserModel::findOne(['user_id' => $model->id]);
        $roleList = AccessRoleModel::find()->all();
        if ($model->load(Yii::$app->request->post())) {
            $list = Yii::$app->request->post('access');
            if (!empty($model->password)) {
                $model->setPassword($model->password);
            }
            $model->save();
            if (empty($model->errors)) {
                $availableAccessIdList = AccessModel::findIdListByVisible();
                $accessUser->load(Yii::$app->request->post());
                if (!empty($list)) {
                    $accessList = [];
                    foreach ($list as $accessId) {
                        if (in_array($accessId, $availableAccessIdList)) {
                            $accessList[$accessId] = ["*"];
                        }
                    }
                    $accessUser->access_list = json_encode($accessList);
                    $accessUser->save();
                } else if(!empty($accessUser->role_id)) {
                    $accessUser->access_list = json_encode([]);
                    $accessUser->save();
                }

                if (!empty($accessUser->errors)) {
                    HtmlHelper::setError(Html::errorSummary($accessUser));
                }
            }
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('更新成功');
            }
            return $this->redirect(['update' . $this->queryString]);
        }

        return $this->render('update', [
            'model' => $model,
            'roleList' => $roleList,
            'accessUser' => $accessUser
        ]);
    }

    public function actionDelete()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = UserModel::findOne(["id" => $id]);
        if(!empty($model)) {
            $model->delete();
            HtmlHelper::setMessage('刪除成功');
        }
        return $this->redirect('index');
    }
}