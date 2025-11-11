<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use backend\widget\NavTab;
use ball\helper\HtmlHelper;
use common\models\VoteModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\VoteModel */
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
                        <h5><?= $model->name ?></h5>
                    </div>
                    <div class="ibox-content">
                        <?= NavTab::widget([
                            'links' => [
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/update?id=" . $model->id,
                                    'label' => $actionLabel . "資訊",
                                    'active' => true
                                ],
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/option?id=" . $model->id,
                                    'label' => $actionLabel . "選項",
                                ],
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/record?id=" . $model->id,
                                    'label' => $actionLabel . "統計",
                                ]
                            ]
                        ]) ?>
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
                                       class="col-sm-2 control-label">名稱</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'name',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入名稱']) ?>
                                    <span class="help-block m-b-none">請填入活動名稱</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">投票限制</label>
                                <div class="col-sm-10">
                                    <?php
                                        echo Html::activeDropDownList($model, 'vote_limit',  VoteModel::$voteLimitLabel,
                                                ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇']);
                                    ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">狀態</label>
                                <div class="col-sm-10">
                                    <?php
                                        if($roleAuthority=='COLUMNIST'){
                                            echo Html::activeDropDownList($model, 'status', ['0' => '待審核'],
                                                ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']);

                                        }else{
                                            echo Html::activeDropDownList($model, 'status', ArrayHelper::merge(['' => '請選擇'], VoteModel::$statusLabel),
                                                ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']);
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">期限類型</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'deadline', VoteModel::$deadlineLabel,
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇期限類型']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div id="deadline-interval"
                                 class="<?= $model->deadline == VoteModel::DEADLINE_OFF ? 'hidden' : '' ?>">
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
                                <div class="col-sm-2"></div>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary mr10">送出</button>
                                    <button type="reset" class="btn btn-default">重填</button>
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
                if (this.value === '<?=VoteModel::DEADLINE_ON?>') {
                    deadlineInterval.classList.remove('hidden');
                    formParams['<?=Html::getInputName($model, "start_time")?>'] = ['', '請輸入開始時間'];
                    formParams['<?=Html::getInputName($model, "end_time")?>'] = ['', '請輸入結束時間'];
                } else if (this.value === '<?=VoteModel::DEADLINE_OFF?>') {
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
