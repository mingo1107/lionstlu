<?php

namespace frontend\controllers;


use ball\helper\HtmlHelper;
use ball\order\OrderStatus;
use common\models\MemberModel;
use common\models\OrdersDetailModel;
use common\models\OrdersModel;
use common\models\OrdersStatusFlowModel;
use common\models\ProductModel;
use common\models\StandardModel;
use frontend\models\CheckoutForm;
use Yii;
use yii\db\Expression;
use yii\helpers\Html;

class CheckoutController extends FrontendController
{

    public function actionIndex()
    {
        $quantity = intval(Yii::$app->request->get('quantity'));
        $standardId = intval(Yii::$app->request->get('sid'));
        $standard = StandardModel::findOnline($standardId);
        if (empty($standard)) {
            return $this->redirect(['/site/index']);
        }
        $product = ProductModel::findOnlineByStandard($standardId);
        if (empty($product)) {
            return $this->redirect(['/site/index']);
        }

        if ($quantity <= 0) {
            $quantity = 1;
        }

        $form = new CheckoutForm();
        if(!Yii::$app->user->isGuest) {
            /**
             * @var $identity MemberModel
             */
            $identity = Yii::$app->user->getIdentity();
            $form->email = $identity->username;
            $form->name = $identity->name;
            $form->district = $identity->district;
            $form->city = $identity->city;
            $form->mobile = $identity->mobile;
            $form->address = $identity->address;
        }
        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                if (Yii::$app->user->isGuest) {
                    if (!MemberModel::exists($form->email)) {
                        $member = new MemberModel(['scenario' => MemberModel::SCENARIO_CREATE]);
                        $member->email = $form->email;
                        $member->username = $form->email;
                        $member->city = $form->city;
                        $member->district = $form->district;
                        $member->name = $form->name;
                        $member->mobile = $form->mobile;
                        $member->zip = $form->zip;
                        $member->address = $form->address;
                        $member->setPassword($form->password);
                        $form->password = '';
                        $form->password2 = '';
                        if (!$member->save()) {
                            HtmlHelper::setError(Html::errorSummary($member));
                            return $this->render('index', ['product' => $product, 'standard' => $standard, 'quantity' => $quantity,
                                'form' => $form]);
                        }
                    } else {
                        HtmlHelper::setError("很抱歉，已經存在相同會員'$form->email'，請選擇其他E-Mail帳號");
                        return $this->render('index', ['product' => $product, 'standard' => $standard, 'quantity' => $quantity,
                            'form' => $form]);
                    }
                } else {
                    /**
                     * @var $member MemberModel
                     */
                    $member = Yii::$app->user->getIdentity();
                }
                $order = new OrdersModel(['scenario' => OrdersModel::SCENARIO_CREATE]);
                $order->no = OrdersModel::generateNo();
                $order->status = OrderStatus::UNPAID;
                $order->email = $form->email;
                $order->name = $form->name;
                $order->member_id = $member->id;
                $order->mobile = $form->mobile;
                $order->address = $form->address;
                $order->shipping_fee = 0;
                $order->zip = $form->zip;
                $order->city = $form->city;
                $order->district = $form->district;
                $order->receiver_email = $form->receiver_email;
                $order->receiver_name = $form->receiver_name;
                $order->receiver_mobile = $form->receiver_mobile;
                $order->receiver_address = $form->receiver_address;
                $order->receiver_zip = $form->receiver_zip;
                $order->receiver_city = $form->receiver_city;
                $order->receiver_district = $form->receiver_district;
                $order->tax = 0;
                $order->net = 0;
                if (!$order->save()) {
                    HtmlHelper::setError(Html::errorSummary($order));
                    return $this->render('index', ['product' => $product, 'standard' => $standard, 'quantity' => $quantity,
                        'form' => $form]);
                }

                $s = StandardModel::findOnline($standardId);
                if (empty($s) || $quantity <= 0) {
                    if (Yii::$app->user->isGuest) {
                        $member->delete();
                    }
                    HtmlHelper::setError('發生錯誤，請稍後再試');
                    return $this->redirect(["index?sid=$standardId"]);
                }
                if (!StandardModel::updateSoldStock($standardId, $quantity)) {
                    // rollback
                    OrdersDetailModel::rollbackStock($order->id);
                    OrdersDetailModel::deleteAllByOrdersId($order->id);
                    $order->delete();
                    if (Yii::$app->user->isGuest) {
                        $member->delete();
                    }
                    HtmlHelper::setError('很抱歉，您選擇的商品規格已售完，請選擇其他規格');
                    return $this->redirect(["index?sid=$standardId"]);
                }
                $orderDetail = new OrdersDetailModel(['scenario' => OrdersDetailModel::SCENARIO_CREATE]);
                $orderDetail->orders_id = $order->id;
                $orderDetail->quantity = $quantity;
                $orderDetail->status = OrderStatus::UNPAID;
                $orderDetail->net = $quantity * $s->price;
                $orderDetail->gross = $orderDetail->net;
                $orderDetail->shipping_fee = 0;
                $orderDetail->tax = 0;
                $order->net += $orderDetail->gross;
                $orderDetail->standard_id = $standardId;
                $orderDetail->save();
                $order->gross = $order->net;
                if ($order->net > 0) {
                    $order->update();
                    $orderFlow = new OrdersStatusFlowModel();
                    $orderFlow->status = $order->status;
                    $orderFlow->orders_id = $order->id;
                    $orderFlow->total = $order->gross;
                    $orderFlow->create_time = new Expression('now()');
                    $orderFlow->orders_detail_id_list = json_encode([$orderDetail->id]);
                    $orderFlow->fee = 0;
                    $orderFlow->shipment_fee = 0;
                    $orderFlow->create_user = OrdersStatusFlowModel::USER_MEMBER;
                    $orderFlow->save();
                    // TODO email member info
                } else {
                    $order->delete();
                    HtmlHelper::setError('沒有選擇任何商品');
                    return $this->redirect(["index?sid=$standardId"]);
                }
                Yii::$app->user->login($member);
                return $this->redirect(["finish?oid=$order->id"]);

            } else {
                $form->password = '';
                $form->password2 = '';
                HtmlHelper::setError(Html::errorSummary($form));
            }
        }

        return $this->render('index', ['product' => $product, 'standard' => $standard, 'quantity' => $quantity,
            'form' => $form]);
    }

    public function actionFinish()
    {
        $oid = Yii::$app->request->get('oid');
        return $this->render('finish', ['oid' => $oid]);
    }
}