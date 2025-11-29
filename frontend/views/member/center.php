<?php

use backend\widget\InlineScript;
use ball\helper\HtmlHelper;
use common\assets\TwCityAsset;
use common\models\MemberModel;
use frontend\assets\FormValidateAsset;
use frontend\widget\Breadcrumbs;
use frontend\widget\MemberNav;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $breadcrumbs \common\models\StandardModel */
/* @var $member \common\models\MemberModel */

FormValidateAsset::register($this);
TwCityAsset::register($this);
?>
<div class="container">
    <?= Breadcrumbs::widget(['links' => $breadcrumbs]) ?>
    <div class="row">
        <?= MemberNav::widget(['index' => 0]) ?>
        <!--會員中心_開始-->
        <!--內容_開始-->
        <div class="col-md-9 col-xs-12 mc-main-body">
            <div class="panel panel-default min-height-300">
                <div class="panel-heading">
                    <h3 class="panel-title">會員資炓修改</h3>
                </div>
                <div class="panel-body">
                    <?= HtmlHelper::displayFlash() ?>
                    <form id="main-form" class="form-horizontal" method="post">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                               value="<?= Yii::$app->request->csrfToken ?>"/>
                        <div class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label">帳號</label>
                            <div class="col-sm-10 col-xs-12">
                                <p class="form-control-static"><?= $member->username ?></p>
                            </div>
                        </div>

                        <div class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label"><span class="note">＊</span>名稱</label>
                            <div class="col-sm-10 col-xs-12">
                                <?= Html::activeTextInput($member, 'name',
                                    ['class' => 'form-control input-lg', 'placeholder' => '名稱']) ?>
                            </div>
                        </div>

                        <div class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label">新密碼</label>
                            <div class="col-sm-10 col-xs-12">
                                <?= Html::activePasswordInput($member, 'password',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入密碼']) ?>
                            </div>
                        </div>
                        <div class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label">重新輸入新密碼</label>
                            <div class="col-sm-10 col-xs-12">
                                <?= Html::activePasswordInput($member, 'password2',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請再次輸入密碼']) ?>
                            </div>
                        </div>
                        <div class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label">手機</label>
                            <div class="col-sm-10 col-xs-12">
                                <?= Html::activeTextInput($member, 'mobile',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入手機',
                                        'data-v-rule' => '', 'data-v-msg' => '請輸入手機']) ?>
                            </div>
                        </div>
                        <div class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label">分會名稱</label>
                            <div class="col-sm-10 col-xs-12">
                                <p class="form-control-static"><?= Html::encode($member->club_name ?: '未設定') ?></p>
                            </div>
                        </div>
                        <div class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label">生日</label>
                            <div class="col-sm-10 col-xs-12">
                                <div class="input-group date">
                                    <?= Html::activeTextInput($member, 'birthday',
                                        ['class' => 'form-control input-lg', 'placeholder' => '請選擇生日',
                                            'readonly' => true]) ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div id="city-selector" class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label">地址</label>
                            <div class="col-md-2 col-sm-2 col-xs-12 ">
                                <?= Html::activeDropDownList($member, 'city', [],
                                    ['class' => 'form-control input-lg']) ?>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 add">
                                <?= Html::activeDropDownList($member, 'district', [],
                                    ['class' => 'form-control input-lg']) ?>
                                <?= Html::activeHiddenInput($member, 'zip') ?>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <?= Html::activeTextInput($member, 'address',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入地址']) ?>
                            </div>
                        </div>
                        <div class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label">性別</label>
                            <div class="col-sm-10 col-xs-12">
                                <?= Html::activeDropDownList($member, 'gender', ArrayHelper::merge(['' => '請選擇'],
                                    MemberModel::$genderLabel),
                                    ['class' => 'form-control input-lg']) ?>
                            </div>
                        </div>
                        <div class="form-group border-dashed">
                            <div class="col-sm-offset-2 col-sm-10 col-xs-12">
                                <button id="submit" type="submit" class="btn btn-success">儲存</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--內容_結束-->
    </div>
</div>
<?php InlineScript::begin() ?>
<script>
    (function () {
        var formParams = {
            '<?= Html::getInputName($member, 'name') ?>': [function () {
                var name = document.getElementById('<?= Html::getInputId($member, 'name') ?>');
                return /^[\u4e00-\u9fa5]+$/.test($.trim(name.value));
            }, '名稱只能輸入中文'],
            '<?= Html::getInputName($member, 'password') ?>': [function () {
                var password = document.getElementById('<?= Html::getInputId($member, 'password') ?>');
                var password2 = document.getElementById('<?= Html::getInputId($member, 'password2') ?>');
                if ($.trim(password.value).length === 0 ||
                    ($.trim(password.value).length !== 0 && password.value === password2.value)) {
                    return true;
                } else {
                    return false;
                }
            }, '請輸入密碼並且密碼必須一致']
        };

        $('#main-form').submit(function () {
            return $(this).formValidate(formParams);
        });

        // 初始化生日 datepicker
        $('#<?=Html::getInputId($member, 'birthday')?>').closest('.input-group.date').datetimepicker({
            format: 'YYYY-MM-DD',
            locale: 'zh-TW',
            maxDate: moment(), // 不能選擇未來的日期
            viewMode: 'years', // 從年份開始選擇
            icons: {
                time: 'glyphicon glyphicon-time',
                date: 'glyphicon glyphicon-calendar',
                up: 'glyphicon glyphicon-chevron-up',
                down: 'glyphicon glyphicon-chevron-down',
                previous: 'glyphicon glyphicon-chevron-left',
                next: 'glyphicon glyphicon-chevron-right',
                today: 'glyphicon glyphicon-screenshot',
                clear: 'glyphicon glyphicon-trash',
                close: 'glyphicon glyphicon-remove'
            }
        });

        new TwCitySelector({
            el: "#city-selector",
            elCounty: "#<?=Html::getInputId($member, 'city')?>", // 在 el 裡查找 dom
            elDistrict: "#<?=Html::getInputId($member, 'district')?>", // 在 el 裡查找 dom
            elZipcode: "#<?=Html::getInputId($member, 'zip')?>", // 在 el 裡查找 dom
            selectedCounty: '<?=$member->city?>',
            selectedDistrict: '<?=$member->district?>',
            countyClassName: "form-control input-lg",
            countyFiledName: "<?=Html::getInputName($member, 'city')?>",
            districtClassName: "form-control input-lg",
            districtFieldName: "<?=Html::getInputName($member, 'district')?>",
            zipcodeFiledName: "<?=Html::getInputName($member, 'zip')?>",
        });
    })();
</script>
<?php InlineScript::end() ?>
