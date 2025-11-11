<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\CKEditor;
use backend\widget\InlineScript;
use backend\widget\NavTab;
use backend\widget\Upload;
use ball\helper\HtmlHelper;
use common\models\ProductModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\ProductModel */
/* @var $media \common\models\Media */
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
                        <?= NavTab::widget([
                            'links' => [
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/update?id=" . $model->id,
                                    'label' => $actionLabel . "產品資訊",
                                    'active' => true
                                ],
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/standard?id=" . $model->id,
                                    'label' => $actionLabel . "規格列表",
                                ]
                            ]
                        ]) ?>
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
                                <label class="col-sm-2 control-label">規格類型</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'standard_type', ProductModel::$standardTypeLabel,
                                        ['class' => 'form-control']) ?>
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
                                <label for="<?= Html::getInputId($model, 'media') ?>"
                                       class="col-sm-2 control-label">商品圖片</label>
                                <div class="col-sm-10">
                                    <?= Upload::widget([
                                        'id' => $model->getMediaInputName('media'),
                                        'name' => $model->getMediaInputName('media'),
                                        'category' => $model->mediaAttribute['media']['category'],
                                        'current' => $media->src,
                                        'crop' => true,
                                        'cropRatio' => 1
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


            $('#main-form').submit(function () {
                return $(this).formValidate(formParams);
            });
        })();
    </script>
<?php InlineScript::end() ?>
