<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use ball\helper\HtmlHelper;
use common\assets\TwCityAsset;
use common\models\AreaModel;
use common\models\MemberModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\MemberModel */

FormValidateAsset::register($this);
TwCityAsset::register($this);
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
                            value="<?= Yii::$app->request->csrfToken ?>" />
                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'member_code') ?>"
                                class="col-sm-2 control-label">會員編號</label>
                            <div class="col-sm-10">
                                <?= Html::activeTextInput(
                                    $model,
                                    'member_code',
                                    ['class' => 'form-control', 'placeholder' => '請輸入會員編號', 'maxlength' => 10]
                                ) ?>
                                <span class="help-block m-b-none">會員編號必須唯一，不可重複</span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">ID</label>
                            <div class="col-lg-10">
                                <p class="form-control-static"><?= $model->username ?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">建立時間</label>
                            <div class="col-lg-10">
                                <p class="form-control-static"><?= $model->create_time ?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">最後更新時間</label>
                            <div class="col-lg-10">
                                <p class="form-control-static"><?= $model->update_time ?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">最後登入ip</label>
                            <div class="col-lg-10">
                                <p class="form-control-static"><?= $model->last_login_ip ?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">最後登入時間</label>
                            <div class="col-lg-10">
                                <p class="form-control-static"><?= $model->last_login_time ?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">登入次數</label>
                            <div class="col-lg-10">
                                <p class="form-control-static"><?= $model->login_count ?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'password') ?>"
                                class="col-sm-2 control-label">密碼</label>
                            <div class="col-sm-10">
                                <?= Html::activePasswordInput(
                                    $model,
                                    'password',
                                    ['class' => 'form-control', 'placeholder' => '留空則不更新密碼']
                                ) ?>
                                <span class="help-block m-b-none">留空則不更新密碼，如需更新請輸入至少8個字元</span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'password2') ?>"
                                class="col-sm-2 control-label">請再輸入一次密碼</label>
                            <div class="col-sm-10">
                                <?= Html::activePasswordInput(
                                    $model,
                                    'password2',
                                    ['class' => 'form-control', 'placeholder' => '留空則不更新密碼']
                                ) ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">狀態</label>
                            <div class="col-sm-10">
                                <?= Html::activeDropDownList(
                                    $model,
                                    'status',
                                    ArrayHelper::merge(['' => '請選擇'], MemberModel::$statusLabel),
                                    ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']
                                ) ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">驗證狀態</label>
                            <div class="col-sm-10">
                                <?= Html::activeDropDownList(
                                    $model,
                                    'validate',
                                    ArrayHelper::merge(['' => '請選擇'], MemberModel::$validateLabel),
                                    ['class' => 'form-control']
                                ) ?>
                                <span class="help-block m-b-none">
                                    「已認證」的會員才能訪問需要權限的內容
                                </span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'period_start') ?>"
                                class="col-sm-2 control-label">會員期限開始</label>
                            <div class="input-group date col-sm-10">
                                <?= Html::activeTextInput(
                                    $model,
                                    'period_start',
                                    ['class' => 'form-control datepicker-start', 'placeholder' => 'YYYY-MM-DD']
                                ) ?>
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-calendar"></i>
                                </span>
                            </div>
                            <!-- <div class="col-sm-offset-2 col-sm-10">
                                <span class="help-block m-b-none">留空表示立即生效</span>
                            </div> -->
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'period_end') ?>"
                                class="col-sm-2 control-label">會員期限結束</label>
                            <div class="input-group date col-sm-10">
                                <?= Html::activeTextInput(
                                    $model,
                                    'period_end',
                                    ['class' => 'form-control datepicker-end', 'placeholder' => 'YYYY-MM-DD']
                                ) ?>
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-calendar"></i>
                                </span>
                            </div>
                            <!-- <div class="col-sm-offset-2 col-sm-10">
                                <span class="help-block m-b-none">留空表示永久有效</span>
                            </div> -->
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'email') ?>"
                                class="col-sm-2 control-label">E-mail</label>
                            <div class="col-sm-10">
                                <?= Html::activeTextInput(
                                    $model,
                                    'email',
                                    ['class' => 'form-control', 'data-v-rule' => 'email', 'data-v-msg' => 'Email格式不正確']
                                ) ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'name') ?>"
                                class="col-sm-2 control-label">名稱</label>
                            <div class="col-sm-10">
                                <?= Html::activeTextInput(
                                    $model,
                                    'name',
                                    ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入名稱']
                                ) ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'mobile') ?>"
                                class="col-sm-2 control-label">手機</label>
                            <div class="col-sm-10">
                                <?= Html::activeTextInput(
                                    $model,
                                    'mobile',
                                    ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入手機']
                                ) ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'area_id') ?>"
                                class="col-sm-2 control-label">區域</label>
                            <div class="col-sm-10">
                                <?php
                                $areaList = AreaModel::findAllForSelect();
                                $areaOptions = ArrayHelper::merge(['0' => '請選擇'], ArrayHelper::map($areaList, 'id', 'area_name'));
                                // 如果 area_id 為 NULL 或空，設為 0
                                $selectedAreaId = !empty($model->area_id) ? $model->area_id : 0;
                                ?>
                                <?= Html::dropDownList(
                                    Html::getInputName($model, 'area_id'),
                                    $selectedAreaId,
                                    $areaOptions,
                                    ['class' => 'form-control']
                                ) ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'birthday') ?>"
                                class="col-sm-2 control-label">生日</label>
                            <div class="input-group date col-sm-10">
                                <?= Html::activeTextInput(
                                    $model,
                                    'birthday',
                                    ['class' => 'form-control']
                                ) ?>
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-calendar"></i>
                                </span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div id="city-selector">
                                <label class="col-sm-2 control-label">所在城市</label>
                                <div class="col-sm-10 margin-bottom-2">
                                    <?= Html::activeDropDownList(
                                        $model,
                                        'city',
                                        [],
                                        ['class' => 'form-control']
                                    ) ?>
                                </div>
                                <label class="col-sm-2 control-label">所在地區</label>
                                <div class="col-sm-10 margin-bottom-2">
                                    <?= Html::activeDropDownList(
                                        $model,
                                        'district',
                                        [],
                                        ['class' => 'form-control']
                                    ) ?>
                                </div>
                                <?= Html::activeHiddenInput($model, 'zip') ?>
                            </div>
                            <label class="col-sm-2 control-label">所在地址</label>
                            <div class="col-sm-10">
                                <?= Html::activeTextInput(
                                    $model,
                                    'address',
                                    ['class' => 'form-control', 'placeholder' => '請輸入地址']
                                ) ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary mr10">送出</button>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php InlineScript::begin() ?>
<script>
    (function() {
        var formParams = {
            '<?= Html::getInputName($model, 'password') ?>': [function() {
                var password = document.getElementById('<?= Html::getInputId($model, 'password') ?>');
                var password2 = document.getElementById('<?= Html::getInputId($model, 'password2') ?>');
                // 如果密碼為空，則不驗證（允許不更新密碼）
                if ($.trim(password.value) === '' && $.trim(password2.value) === '') {
                    return true;
                }
                // 如果密碼有輸入，則驗證長度和一致性
                if ($.trim(password.value).length >= 8 && password.value === password2.value) {
                    return true;
                } else {
                    return false;
                }
            }, '密碼長度必須大於8個字元而且密碼必須一致（留空則不更新密碼）']
        };

        $('#main-form').submit(function() {
            return $(this).formValidate(formParams);
        });

        new TwCitySelector({
            el: "#city-selector",
            elCounty: "#<?= Html::getInputId($model, 'city') ?>", // 在 el 裡查找 dom
            elDistrict: "#<?= Html::getInputId($model, 'district') ?>", // 在 el 裡查找 dom
            elZipcode: "#<?= Html::getInputId($model, 'zip') ?>", // 在 el 裡查找 dom
            selectedCounty: '<?= Html::encode($model->city ?: '') ?>',
            selectedDistrict: '<?= Html::encode($model->district ?: '') ?>',
            countyClassName: "form-control margin-bottom-2",
            countyFiledName: "<?= Html::getInputName($model, 'city') ?>",
            districtClassName: "form-control margin-bottom-2",
            districtFieldName: "<?= Html::getInputName($model, 'district') ?>",
            zipcodeFiledName: "<?= Html::getInputName($model, 'zip') ?>",
        });

        // 會員期限日期選擇器
        $('.datepicker-start, .datepicker-end').parent('.input-group.date').datetimepicker({
            format: 'YYYY-MM-DD',
            locale: 'zh-tw',
            useCurrent: false,
            showClear: true,
            showClose: true,
            toolbarPlacement: 'top',
            widgetPositioning: {
                horizontal: 'auto',
                vertical: 'bottom'
            }
        });

        // 設定開始日期變更時，更新結束日期的最小值
        $('.datepicker-start').parent('.input-group.date').on('dp.change', function(e) {
            var startDate = e.date;
            $('.datepicker-end').parent('.input-group.date').data('DateTimePicker').minDate(startDate);
        });

        // 設定結束日期變更時，更新開始日期的最大值
        $('.datepicker-end').parent('.input-group.date').on('dp.change', function(e) {
            var endDate = e.date;
            $('.datepicker-start').parent('.input-group.date').data('DateTimePicker').maxDate(endDate);
        });
    })();
</script>
<?php InlineScript::end() ?>