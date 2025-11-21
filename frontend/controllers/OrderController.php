<?php

namespace frontend\controllers;


use ball\helper\Pagination;
use common\models\OrdersDetailModel;
use common\models\OrdersModel;
use common\models\ProductModel;
use common\models\StandardModel;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class OrderController extends FrontendController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'detail'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $breadcrumbs = [
            ['url' => '/member/center', 'label' => '會員中心'],
            ['label' => '歷史訂單查詢']
        ];

        $start = Pagination::getOffset();
        $list = OrdersModel::search(['member_id' => Yii::$app->user->getId()]);
        $count = OrdersModel::count(['member_id' => Yii::$app->user->getId()]);
        $orderDetailList = [];
        foreach ($list as $o) {
            $orderDetailList[$o->id] = OrdersDetailModel::findAllByOrderId($o->id);
        }
        return $this->render('index', ['list' => $list, 'orderDetailList' => $orderDetailList,
            "start" => $start, 'count' => $count, 'breadcrumbs' => $breadcrumbs]);
    }

    public function actionDetail()
    {
        $breadcrumbs = [
            ['url' => '/member/center', 'label' => '會員中心'],
            ['label' => '歷史訂單查詢']
        ];

        $oid = Yii::$app->request->get('o');
        $order = OrdersModel::findOne(['no' => $oid, 'member_id' => Yii::$app->user->getId()]);
        if (empty($order)) {
            return $this->redirect(['/order/index']);
        }
        $orderDetail = OrdersDetailModel::findAllByOrderId($order->id)[0];
        $product = ProductModel::findOne(['id' => $orderDetail->product_id]);
        $standard = StandardModel::findOne(['id' => $orderDetail->standard_id]);
        return $this->render('detail', ['order' => $order, 'product' => $product, 'standard' => $standard,
            'breadcrumbs' => $breadcrumbs, 'orderDetail' => $orderDetail]);
    }
}