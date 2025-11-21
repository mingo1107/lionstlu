<?php

namespace backend\controllers;


use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\UserModel;
use common\models\VoteModel;
use common\models\VoteOptionModel;
use common\models\AccessUserModel;
use common\models\AccessRoleModel;
use Yii;
use yii\helpers\Html;

class VoteController extends BackendController
{
    protected $actionLabel = '投票活動';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword']);
        $accessUser = AccessUserModel::findOne(['user_id' => Yii::$app->user->getIdentity()->id]);
        if($accessUser->role_id==7){
            $search['user_id'] = Yii::$app->user->getIdentity()->id;
        }
        $list = VoteModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = VoteModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }

    public function actionCreate()
    {
        $model = new VoteModel(['scenario' => VoteModel::SCENARIO_CREATE]);
        $vendorList = UserModel::findAllOnlineVendor();
        if ($model->load(Yii::$app->request->post())) {
            $model->user_id = Yii::$app->user->getIdentity()->id;
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('建立成功，請新增投票選項');
            }
            return $this->redirect(['option?id=' . $model->id]);
        } else {

            $accessUser = AccessUserModel::findOne(['user_id' => Yii::$app->user->getIdentity()->id]);
            $roleAuthority = AccessRoleModel::roleAuthority($accessUser->role_id);
            //$need_verify = ($roleAuthority == 'COLUMNIST') ? 1 : 0;

            return $this->render('create', ['model' => $model, 'vendorList' => $vendorList, 'roleAuthority' => $roleAuthority]);
        }
    }

    public function actionUpdate()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = VoteModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $model->scenario = VoteModel::SCENARIO_UPDATE;
        $vendorList = UserModel::findAllOnlineVendor();
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('更新成功');
            }
            return $this->redirect(Yii::$app->request->referrer);
        } else {

            $accessUser = AccessUserModel::findOne(['user_id' => Yii::$app->user->getIdentity()->id]);
            $roleAuthority = AccessRoleModel::roleAuthority($accessUser->role_id);
            //$need_verify = ($roleAuthority == 'COLUMNIST') ? 1 : 0;

            return $this->render('update', ['model' => $model, 'vendorList' => $vendorList, 'roleAuthority' => $roleAuthority]);
        }
    }

    public function actionOption()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = VoteModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $list = VoteOptionModel::findAllByVoteId($model->id);
        $this->applyBreadcrumbsAndTitle($this->actionLabel . '選項');
        $update = Yii::$app->request->post('update');
        if ($update == 1) {
            if (!empty($list)) { // 只能修改不能新增刪除
                foreach ($list as $option) {
                    $name = Yii::$app->request->post("option-$option->id");
                    VoteOptionModel::updateNameById($name, $option->id);
                }
            } else { // 沒資料可以新增刪除
                $optionList = Yii::$app->request->post('option');
                foreach ($optionList as $option) {
                    $o = new VoteOptionModel();
                    $o->name = $option;
                    $o->vote_id = $model->id;
                    $o->insert();
                }
            }
            HtmlHelper::setMessage('更新成功');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->render('option', ['model' => $model, 'list' => $list]);
        }
    }

    public function actionRecord()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = VoteModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $list = VoteOptionModel::find()->where(["vote_id" => $model->id])->orderBy(["count" => SORT_DESC])->all();
//        foreach($list as &$op) {
//            $count = VoteRecordModel::countByOptionId($op->id);
//            $op->count = $count;
//        }
        $this->applyBreadcrumbsAndTitle($this->actionLabel . '統計');
        return $this->render('record', ['model' => $model, 'list' => $list]);
    }

    public function actionDelete()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = VoteModel::findOne(["id" => $id]);
        if (!empty($model)) {
            $model->delete();
            VoteOptionModel::deleteAll(["vote_id" => $model->id]);
            HtmlHelper::setMessage('刪除成功');
        }
        return $this->redirect('index');
    }
}
