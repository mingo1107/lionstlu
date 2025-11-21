<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use ball\helper\HtmlHelper;
use ball\order\OrderStatus;
use common\assets\TwCityAsset;
use common\assets\VueAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\OrdersModel */
/* @var $mediaList \common\models\Media[] */
/* @var $coverMedia \common\models\Media */
/* @var $item \common\models\ProductModel|\common\models\VoteModel */
/* @var $categoryList \common\models\ArticleCategoryModel[] */

FormValidateAsset::register($this);
TwCityAsset::register($this);
VueAsset::register($this);
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
                <form id="main-form" name="main-form" class="form-horizontal" method="post" action="<?= $qs ?>">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5><?= $title ?></h5>
                        </div>
                        <div class="ibox-content">
                            <?= HtmlHelper::displayFlash() ?>
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                   value="<?= Yii::$app->request->csrfToken ?>"/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">訂單編號</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $model->no ?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'status') ?>"
                                       class="col-sm-2 control-label">訂單狀態</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'status', OrderStatus::$labels,
                                        ['class' => 'form-control', 'data-v-rule' => '']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">商品</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $model->details[0]->standard->product->name ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">數量</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $model->details[0]->quantity ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">訂單金額</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $model->gross ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">建立時間</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $model->create_time ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">最後更新時間</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?= $model->update_time ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                        </div>
                    </div>
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>購買人資訊</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">姓名</label>
                                <div class="col-sm-10">
                                    <?php echo Html::activeTextInput($model, "name", ["class" => "form-control", "v-model" => "form.name",
                                        "data-v-rule" => "", "data-v-msg" => ""]) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">電話</label>
                                <div class="col-sm-10">
                                    <?php echo Html::activeTextInput($model, "mobile", ["class" => "form-control", "v-model" => "form.mobile",
                                        "data-v-rule" => "", "data-v-msg" => ""]) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">E-Mail</label>
                                <div class="col-sm-10">
                                    <?php echo Html::activeTextInput($model, 'email', ['class' => 'form-control',
                                        "data-v-rule" => "", "data-v-msg" => "", "v-model" => "form.email"]) ?>
                                </div>
                            </div>
                            <div id="city-selector">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">郵遞區號</label>
                                    <div class="col-sm-10">
                                        <?php echo Html::activeTextInput($model, 'zip', ['class' => 'form-control', "data-v-rule" => "", "data-v-msg" => ""
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">縣市</label>
                                    <div class="col-sm-10">
                                        <?php echo Html::activeDropDownList($model, 'city', [], ['class' => 'form-control', "data-v-rule" => "", "data-v-msg" => ""
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">區</label>
                                    <div class="col-sm-10">
                                        <?php echo Html::activeDropDownList($model, 'district', [], ['class' => 'form-control',
                                            "v-model" => "form.district", "data-v-rule" => "", "data-v-msg" => ""]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地址</label>
                                <div class="col-sm-10">
                                    <?php echo Html::activeTextInput($model, 'address', ['class' => 'form-control', "data-v-rule" => "", "data-v-msg" => ""]) ?>
                                </div>
                            </div>
                        </div>

                        <div class="ibox-title">
                            <h5>收件人資訊</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">收件人姓名</label>
                                <div class="col-sm-10">
                                    <?php echo Html::activeTextInput($model, "receiver_name", ["class" => "form-control", "v-model" => "form.name",
                                        "data-v-rule" => "", "data-v-msg" => ""]) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">收件人電話</label>
                                <div class="col-sm-10">
                                    <?php echo Html::activeTextInput($model, "receiver_mobile", ["class" => "form-control", "v-model" => "form.receiver_mobile",
                                        "data-v-rule" => "", "data-v-msg" => ""]) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">收件人E-Mail</label>
                                <div class="col-sm-10">
                                    <?php echo Html::activeTextInput($model, 'receiver_email', ['class' => 'form-control',
                                        "data-v-rule" => "", "data-v-msg" => "", "v-model" => "form.receiver_email"]) ?>
                                </div>
                            </div>
                            <div id="receiver-city-selector">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">收件人郵遞區號</label>
                                    <div class="col-sm-10">
                                        <?php echo Html::activeTextInput($model, 'receiver_zip', ['class' => 'form-control', "data-v-rule" => "", "data-v-msg" => ""
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">收件人縣市</label>
                                    <div class="col-sm-10">
                                        <?php echo Html::activeDropDownList($model, 'receiver_city', [], ['class' => 'form-control', "data-v-rule" => "", "data-v-msg" => ""
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">收件人區</label>
                                    <div class="col-sm-10">
                                        <?php echo Html::activeDropDownList($model, 'receiver_district', [], ['class' => 'form-control',
                                            "v-model" => "form.receiver_district", "data-v-rule" => "", "data-v-msg" => ""]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">收件人地址</label>
                                <div class="col-sm-10">
                                    <?php echo Html::activeTextInput($model, 'receiver_address', ['class' => 'form-control', "data-v-rule" => "", "data-v-msg" => ""]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary mr10">送出</button>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php InlineScript::begin() ?>
    <script>
        (function () {
            var vm = new Vue({
                el: '#main-form',
                data: function () {
                    return {
                        form: <?=json_encode($model->getAttributes())?>,
                        product: <?=json_encode($model->details[0]->standard->product->getAttributes())?>,
                        standard: <?=json_encode($model->details[0]->standard->getAttributes())?>,
                    };
                },
                created: function () {
                },
                mounted: function () {
                    this.citySelector(
                        "#city-selector",
                        "#<?php echo Html::getInputId($model, 'city')?>",
                        "#<?php echo Html::getInputId($model, 'district')?>",
                        "#<?php echo Html::getInputId($model, 'zip')?>",
                        this.form.city,
                        this.form.district,
                        "<?php echo Html::getInputName($model, 'city')?>",
                        "<?php echo Html::getInputName($model, 'district')?>",
                        "<?php echo Html::getInputName($model, 'zip')?>");
                    this.citySelector(
                        "#receiver-city-selector",
                        "#<?php echo Html::getInputId($model, 'receiver_city')?>",
                        "#<?php echo Html::getInputId($model, 'receiver_district')?>",
                        "#<?php echo Html::getInputId($model, 'receiver_zip')?>",
                        this.form.receiver_city,
                        this.form.receiver_district,
                        "<?php echo Html::getInputName($model, 'receiver_city')?>",
                        "<?php echo Html::getInputName($model, 'receiver_district')?>",
                        "<?php echo Html::getInputName($model, 'receiver_zip')?>");
                },
                watch: {
                    // invoiceCarrier: function () {
                    //     this.invoiceCarrierChange();
                    // }
                },
                computed: {
                    // invoiceCarrier: function () {
                    //     return this.form.invoice_carrier_type;
                    // }
                },
                methods: {
                    //invoiceCarrierChange: function () {
                    //    if (this.form.invoice_carrier_type === "<?//=InvoiceCarrier::MOBILE?>//") {
                    //        this.displayInvoiceCarrierMobile = '';
                    //        this.displayInvoiceCarrierNature = 'd-none';
                    //    } else if (this.form.invoice_carrier_type === "<?//=InvoiceCarrier::NATURAL_PERSON?>//") {
                    //        this.displayInvoiceCarrierMobile = 'd-none';
                    //        this.displayInvoiceCarrierNature = '';
                    //    } else {
                    //        this.displayInvoiceCarrierMobile = 'd-none';
                    //        this.displayInvoiceCarrierNature = 'd-none';
                    //    }
                    //},
                    citySelector: function (el, elCounty, elDistrict, elZipcode, city, district, countyFiledName, districtFieldName, zipcodeFiledName) {
                        new TwCitySelector({
                            el: el,
                            elCounty: elCounty, // 在 el 裡查找 dom
                            elDistrict: elDistrict, // 在 el 裡查找 dom
                            elZipcode: elZipcode, // 在 el 裡查找 dom
                            selectedCounty: city,
                            selectedDistrict: district,
                            countyClassName: "form-control",
                            countyFiledName: countyFiledName,
                            districtClassName: "form-control",
                            districtFieldName: districtFieldName,
                            zipcodeFiledName: zipcodeFiledName,
                        });
                    },
                    checkout: function () {
                        var params = {};
                        //if (this.form.invoice_carrier_type != '<?php //echo InvoiceCarrier::MEMBER ?>//') {
                        //    params['<?php //echo Html::getInputName($model, "invoice_carrier_id")?>//'] = ['', ''];
                        // }
                        //params['<?php //echo Html::getInputName($model, "invoice_carrier_type")?>//'] = ['', ''];
                        var $model = $('#main');
                        if ($model.formValidate(params)) {
                            $model.submit();
                        } else {
                            return false;
                        }
                    },
                },
            });

            $('#main-form').submit(function () {
                return $(this).formValidate();
            });
        })();
    </script>
<?php InlineScript::end() ?>