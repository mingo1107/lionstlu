<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use backend\widget\NavTab;
use ball\helper\HtmlHelper;
use common\models\ProductModel;
use common\models\StandardModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\StandardModel */
/* @var $product \common\models\ProductModel */

FormValidateAsset::register($this);
?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2><?= $title ?></h2>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []
            ]) ?>
        </div>
        <div class="col-lg-2"></div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= $title ?></h5>
                    </div>
                    <div class="ibox-content">
                        <?= HtmlHelper::displayFlash() ?>
                        <?= NavTab::widget([
                            'links' => [
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/update?id=" . $product->id,
                                    'label' => $actionLabel . "產品資訊"
                                ],
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/standard?id=" . $product->id,
                                    'label' => $actionLabel . "規格列表"
                                ],
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/standard-create?id=" . $product->id,
                                    'label' => $actionLabel . "建立規格",
                                    'active' => true
                                ]
                            ]
                        ]) ?>
                        <form id="main-form" name="main-form" class="form-horizontal" method="post" action="<?= $qs ?>">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                   value="<?= Yii::$app->request->csrfToken ?>"/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">商品名稱</label>
                                <div class="col-lg-10">
                                    <p class="form-control-static"><?= $product->name ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">規格類型</label>
                                <div class="col-lg-10">
                                    <p class="form-control-static"><?= ProductModel::$standardTypeLabel[$product->standard_type] ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">商品狀態</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'status', ArrayHelper::merge(['' => '請選擇'], StandardModel::$statusLabel),
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'name') ?>"
                                       class="col-sm-2 control-label">規格名稱</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'name',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入規格名稱']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <?php if ($product->standard_type == ProductModel::STANDARD_TYPE_DOUBLE): ?>
                                <div class="form-group">
                                    <label for="<?= Html::getInputId($model, 'name2') ?>"
                                           class="col-sm-2 control-label">第二規格名稱</label>
                                    <div class="col-sm-10">
                                        <?= Html::activeTextInput($model, 'name2',
                                            ['class' => 'form-control']) ?>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'stock') ?>"
                                       class="col-sm-2 control-label">規格庫存</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'stock',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '庫存必須為0或正數']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'original_price') ?>"
                                       class="col-sm-2 control-label">規格原價</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'original_price',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '原價必須為0或正數']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'price') ?>"
                                       class="col-sm-2 control-label">規格價格</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'price',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '價格必須為0或正數']) ?>
                                    <span class="help-block m-b-none">網站實際販售價格</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary mr10">送出</button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php InlineScript::begin() ?>
    <script>
        (function () {
            $('#main-form').submit(function () {
                var params = {};
                <?php if ($product->standard_type == ProductModel::STANDARD_TYPE_DOUBLE): ?>
                params['<?=Html::getInputName($model, 'name2')?>'] = ['', '請輸入第二規格名稱'];
                <?php endif;?>
                return $(this).formValidate(params);
            });
        })();
    </script>
<?php InlineScript::end() ?>