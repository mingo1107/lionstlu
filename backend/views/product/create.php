<?php

use backend\widget\Breadcrumbs;
use backend\widget\CKEditor;
use backend\widget\InlineScript;
use backend\widget\Upload;
use ball\helper\HtmlHelper;
use common\models\ProductModel;
use frontend\assets\FormValidateAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\ProductModel */
/* @var $standard \common\models\StandardModel */
/* @var $vendorList \common\models\UserModel[] */

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
                        <form id="main-form" name="main-form" class="form-horizontal" method="post" action="<?= $qs ?>">
                            <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
                                   value="<?= yii::$app->request->csrfToken ?>"/>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'owner_id') ?>"
                                       class="col-sm-2 control-label">供應商</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'owner_id',
                                        ArrayHelper::merge([0 => '無'], ArrayHelper::map($vendorList, 'id', 'name')),
                                        ['class' => 'form-control']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'name') ?>"
                                       class="col-sm-2 control-label">商品名稱</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'name',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入名稱']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'sn') ?>"
                                       class="col-sm-2 control-label">商品編號</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'sn',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入商品編號']) ?>
                                    <span class="help-block m-b-none">建立後不可修改</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">商品狀態</label>
                                <div class="col-sm-10">
                                    <?php
                                        if($roleAuthority=='COLUMNIST'){
                                            echo Html::activeDropDownList($model, 'status', ['0' => '待審核'],
                                                ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']);

                                        }else{
                                            echo Html::activeDropDownList($model, 'status', ArrayHelper::merge(['' => '請選擇'], ProductModel::$statusLabel),
                                                ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']);
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">販售期限</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'deadline', ProductModel::$deadlineLabel,
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇販售期限']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div id="deadline-interval"
                                 class="<?= $model->deadline == ProductModel::DEADLINE_OFF ? 'hidden' : '' ?>">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">時間區間</label>
                                    <div class="col-sm-10">
                                        <div class="input-group date margin-1">
                                            <?= Html::activeTextInput($model, 'start_time',
                                                ['class' => 'form-control', 'placeholder' => '開始時間']) ?>
                                            <span class="input-group-addon">
                                                <i class="glyphicon glyphicon-calendar"></i>
                                            </span>
                                        </div>
                                        <div class="input-group date margin-1">
                                            <?= Html::activeTextInput($model, 'end_time',
                                                ['class' => 'form-control', 'placeholder' => '結束時間']) ?>
                                            <span class="input-group-addon">
                                                <i class="glyphicon glyphicon-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">規格類型</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'standard_type', ProductModel::$standardTypeLabel,
                                        ['class' => 'form-control']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($standard, 'name') ?>"
                                       class="col-sm-2 control-label">規格名稱</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($standard, 'name',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入規格名稱']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div id="standard-double"
                                 class="<?= $model->standard_type == ProductModel::STANDARD_TYPE_SINGLE ? 'hidden' : '' ?>">
                                <div class="form-group">
                                    <label for="<?= Html::getInputId($standard, 'name2') ?>"
                                           class="col-sm-2 control-label">第二規格名稱</label>
                                    <div class="col-sm-10">
                                        <?= Html::activeTextInput($standard, 'name2',
                                            ['class' => 'form-control']) ?>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($standard, 'stock') ?>"
                                       class="col-sm-2 control-label">規格庫存</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($standard, 'stock',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '庫存必須為0或正數']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($standard, 'original_price') ?>"
                                       class="col-sm-2 control-label">規格原價</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($standard, 'original_price',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '原價必須為0或正數']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($standard, 'price') ?>"
                                       class="col-sm-2 control-label">規格價格</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($standard, 'price',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '價格必須為0或正數']) ?>
                                    <span class="help-block m-b-none">網站實際販售價格</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'media') ?>"
                                       class="col-sm-2 control-label">商品圖片</label>
                                <div class="col-sm-10">
                                    <?= Upload::widget([
                                        'id' => $model->getMediaInputName('media'),
                                        'name' => $model->getMediaInputName('media'),
                                        'category' => $model->mediaAttribute['media']['category'],
                                        'crop' => true,
                                        'cropRatio' => 1,
                                    ]); ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'intro') ?>"
                                       class="col-sm-2 control-label">商品介紹</label>
                                <div class="col-sm-10">
                                    <?= CKEditor::widget([
                                        'model' => $model,
                                        'attribute' => 'intro',
                                        'options' => ['class' => 'form-control']
                                    ]) ?>
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
            var formParams = {};
            var deadlineInterval = document.getElementById('deadline-interval');
            var standardDouble = document.getElementById('standard-double');
            $('#<?=Html::getInputId($model, 'deadline')?>').change(function () {
                if (this.value === '<?=ProductModel::DEADLINE_ON?>') {
                    deadlineInterval.classList.remove('hidden');
                    formParams['<?=Html::getInputName($model, "start_time")?>'] = ['', '請輸入開始時間'];
                    formParams['<?=Html::getInputName($model, "end_time")?>'] = ['', '請輸入結束時間'];
                } else if (this.value === '<?=ProductModel::DEADLINE_OFF?>') {
                    deadlineInterval.classList.add('hidden');
                    delete formParams['<?=Html::getInputName($model, "start_time")?>'];
                    delete formParams['<?=Html::getInputName($model, "end_time")?>'];
                }
            });

            $('#<?=Html::getInputId($model, 'standard_type')?>').change(function () {
                if (this.value === '<?=ProductModel::STANDARD_TYPE_DOUBLE?>') {
                    standardDouble.classList.remove('hidden');
                    formParams['<?=Html::getInputName($standard, "name2")?>'] = ['', '請輸入第二規格名稱'];
                } else if (this.value === '<?=ProductModel::STANDARD_TYPE_SINGLE?>') {
                    standardDouble.classList.add('hidden');
                    delete formParams['<?=Html::getInputName($standard, "name2")?>'];
                }
            });


            $('#main-form').submit(function () {
                return $(this).formValidate(formParams);
            });
        })();
    </script>
<?php InlineScript::end() ?>
