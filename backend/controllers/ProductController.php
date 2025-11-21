<?php

namespace backend\controllers;


use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\ProductModel;
use common\models\StandardModel;
use common\models\UserModel;
use common\models\AccessUserModel;
use common\models\AccessRoleModel;
use Yii;
use yii\helpers\Html;

class ProductController extends BackendController
{
    protected $actionLabel = '商品';

    // Product
    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword']);
        $accessUser = AccessUserModel::findOne(['user_id' => Yii::$app->user->getIdentity()->id]);
        if($accessUser->role_id==7){
            $search['user_id'] = Yii::$app->user->getIdentity()->id;
        }
        $list = ProductModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = ProductModel::count($search);
        return $this->render('index', ['list' => $list, 'start' => $start, 'count' => $count]);
    }

    public function actionCreate()
    {
        $model = new ProductModel(['scenario' => ProductModel::SCENARIO_CREATE]);
        $standard = new StandardModel(['scenario' => StandardModel::SCENARIO_CREATE]);
        $vendorList = UserModel::findAllOnlineVendor();
        if ($model->load(Yii::$app->request->post()) && $standard->load(Yii::$app->request->post())) {
            $model->user_id = Yii::$app->user->getIdentity()->id;
            if ($model->save()) {
                $standard->status = StandardModel::STATUS_ONLINE;
                $standard->sn = $model->sn;
                $standard->product_id = $model->id;
                $standard->save();
            }
            if (!empty($model->errors) || !empty($standard->errors)) {
                HtmlHelper::setError(Html::errorSummary($model) . Html::errorSummary($standard));
            } else {
                HtmlHelper::setMessage('建立成功');
            }
            return $this->redirect(['update' . $this->queryString]);
        } else {

            $accessUser = AccessUserModel::findOne(['user_id' => Yii::$app->user->getIdentity()->id]);
            $roleAuthority = AccessRoleModel::roleAuthority($accessUser->role_id);
            //$need_verify = ($roleAuthority == 'COLUMNIST') ? 1 : 0;

            return $this->render('create', ['model' => $model, 'standard' => $standard, 'vendorList' => $vendorList, 'roleAuthority' => $roleAuthority]);
        }
    }

    public function actionUpdate()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = ProductModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $model->scenario = ProductModel::SCENARIO_UPDATE;
        $media = $model->serializeMedia('media');
        $vendorList = UserModel::findAllOnlineVendor();
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('更新成功');
            }
            return $this->redirect(['update' . $this->queryString]);
        } else {

            $accessUser = AccessUserModel::findOne(['user_id' => Yii::$app->user->getIdentity()->id]);
            $roleAuthority = AccessRoleModel::roleAuthority($accessUser->role_id);
            //$need_verify = ($roleAuthority == 'COLUMNIST') ? 1 : 0;

            return $this->render('update', ['model' => $model, 'media' => $media, 'vendorList' => $vendorList, 'roleAuthority' => $roleAuthority]);
        }
    }

    public function actionDelete()
    {
        $id = intval(Yii::$app->request->get('id'));
        StandardModel::deleteAll(["product_id" => $id]);
        ProductModel::deleteAll(['id' => $id]);
        HtmlHelper::setMessage('刪除成功');
        return $this->redirect(['index' . $this->queryString]);
    }
    // Product end

    // Standard
    public function actionStandard()
    {
        $this->applyBreadcrumbsAndTitle('規格管理');
        $id = intval(Yii::$app->request->get('id'));
        $product = ProductModel::findOne(['id' => $id]);
        if (empty($product)) {
            return $this->redirect(['index']);
        }
        $list = StandardModel::findByProductId($id);
        return $this->render('standard', ['product' => $product, 'list' => $list]);
    }

    public function actionStandardCreate()
    {
        $this->applyBreadcrumbsAndTitle('建立規格');
        $id = intval(Yii::$app->request->get('id'));
        $product = ProductModel::findOne(['id' => $id]);
        if (empty($product)) {
            return $this->redirect(['index']);
        }

        $model = new StandardModel(['scenario' => StandardModel::SCENARIO_CREATE]);
        if ($model->load(Yii::$app->request->post())) {
            $model->product_id = $product->id;
            $model->sn = $product->sn;
            if (!$model->save()) {
                HtmlHelper::setError(Html::errorSummary($model) );
            } else {
                HtmlHelper::setMessage('建立成功');
            }
            return $this->redirect(['standard' . $this->queryString]);
        } else {
            return $this->render('standard-create', ['model' => $model, 'product' => $product]);
        }
    }

    public function actionStandardUpdate()
    {
        $this->applyBreadcrumbsAndTitle('編輯規格');
        $id = intval(Yii::$app->request->get('id'));
        $sid = intval(Yii::$app->request->get('sid'));
        $product = ProductModel::findOne(['id' => $id]);
        if (empty($product)) {
            return $this->redirect(['index']);
        }

        $model = StandardModel::findOne(['product_id' => $id, 'id' => $sid]);
        if ($model->load(Yii::$app->request->post())) {
            $model->scenario = StandardModel::SCENARIO_UPDATE;
            if (!$model->save()) {
                HtmlHelper::setError(Html::errorSummary($model) );
            } else {
                HtmlHelper::setMessage('更新成功');
            }
            return $this->redirect(['standard-update' . $this->queryString]);
        } else {
            return $this->render('standard-update', ['model' => $model, 'product' => $product]);
        }
    }

    public function actionStandardDelete()
    {
        $id = intval(Yii::$app->request->get('id'));
        StandardModel::deleteAll(['id' => $id]);
        HtmlHelper::setMessage('刪除成功');
        return $this->redirect(['index' . $this->queryString]);
    }
    // Standard end
}
