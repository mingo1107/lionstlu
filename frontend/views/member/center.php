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
                        <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
                               value="<?= yii::$app->request->csrfToken ?>"/>
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
                                    ['class' => 'form-control input-lg', 'placeholder' => '名稱',
                                        'data-v-rule' => '', 'data-v-msg' => '請輸入名稱']) ?>
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
                            <label for="" class="col-sm-2 col-xs-12 control-label">生日</label>
                            <div class="col-md-2 col-sm-2 col-xs-12 add">
                                <select class="form-control input-lg">
                                    <option>年</option>
                                    <?php for ($i = date('Y'); $i >= 1900; --$i): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 add">
                                <select class="form-control input-lg">
                                    <option>月</option>
                                    <?php for ($i = 1; $i <= 12; ++$i): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 add">
                                <select class="form-control input-lg">
                                    <option>日</option>
                                    <?php for ($i = 1; $i <= 31; ++$i): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div id="city-selector" class="form-group border-dashed">
                            <label for="" class="col-sm-2 col-xs-12 control-label"><span class="note">＊</span>地址</label>
                            <div class="col-md-2 col-sm-2 col-xs-12 ">
                                <?= Html::activeDropDownList($member, 'city', [],
                                    ['class' => 'form-control input-lg', 'data-v-rule' => '', 'data-v-msg' => '請輸入所在城市']) ?>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 add">
                                <?= Html::activeDropDownList($member, 'district', [],
                                    ['class' => 'form-control input-lg', 'data-v-rule' => '', 'data-v-msg' => '請輸入所在地區']) ?>
                                <?= Html::activeHiddenInput($member, 'zip') ?>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <?= Html::activeTextInput($member, 'address',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入地址',
                                        'data-v-rule' => '', 'data-v-msg' => '請輸入地址']) ?>
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

