<?php

use backend\widget\InlineScript;
use ball\api\ResponseCode;
use ball\helper\HtmlHelper;
use ball\util\Url;
use common\models\AreaModel;
use frontend\assets\FormValidateAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \frontend\models\LoginForm */
/* @var $signupModel \common\models\MemberModel */
/* @var $showSignup bool 是否顯示註冊表單 */
/* @var $areaList \common\models\AreaModel[] */

FormValidateAsset::register($this);
?>
<?php if (yii::$app->user->isGuest): ?>
    <!--會員登入-開始-->
    <form id="login-form" name="login-form" method="post" style="<?= $showSignup ? 'display:none;' : '' ?>">
        <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
            value="<?= yii::$app->request->csrfToken ?>" />
        <input type="hidden" name="action" value="login" />
        <div class="modal-body step step-1 container" id="login-step">
            <div class="signin-row">
                <h3>會員登入</h3>
                <fieldset>
                    <!-- <a class="btn btn-block btn-fb btn-xlg" href="<?= Url::to("/member/fb-login") ?>">
                        <i class="fa fa-facebook-official mr10" aria-hidden="true"></i>facebook帳號登入
                    </a>
                    <center>
                        <h4 style="color:#75787b">或</h4>
                    </center> -->
                    <div class="form-group">
                        <?= Html::activeTextInput(
                            $model,
                            'username',
                            [
                                'placeholder' => 'E-Mail',
                                'autofocus' => '1',
                                'class' => 'form-control input-lg',
                                'data-v-rule' => 'email',
                                'data-v-msg' => 'E-Mail格式不正確'
                            ]
                        ) ?>
                    </div>
                    <div class="form-group">
                        <?= Html::activePasswordInput(
                            $model,
                            'password',
                            [
                                'placeholder' => '密碼',
                                'class' => 'form-control input-lg',
                                'data-v-rule' => '',
                                'data-v-msg' => '請輸入密碼'
                            ]
                        ) ?>
                    </div>
                    <?= HtmlHelper::displayFlash() ?>
                    <div class="checkbox">
                        <!--                        <label>-->
                        <!--                            <input name="--><? //= Html::getInputName($model, 'rememberMe') 
                                                                        ?><!--" type="checkbox"-->
                        <!--                                   value="1" checked="checked">-->
                        <!--                            保持登入 </label>-->
                        <a href="<?= Url::to("/member/forget-password") ?>" class="ml10 step step-1"
                            data-step="1">
                            <i class="fa fa-unlock-alt mr5" aria-hidden="true"></i>忘記密碼</a>
                    </div>
                    <input class="btn btn-success btn-block mt15 mb10 btn-xlg" type="submit" value="登入">
                    <button type="button" class="btn btn-block btn-xlg btn-success-outline"
                        onclick="showSignup()">註冊新會員
                    </button>
                </fieldset>
            </div>
        </div>
    </form>

    <!--會員註冊-開始-->
    <form id="signup-form" name="signup-form" method="post" style="<?= $showSignup ? '' : 'display:none;' ?>">
        <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
            value="<?= yii::$app->request->csrfToken ?>" />
        <input type="hidden" name="action" value="signup" />
        <div class="modal-body step step-3 container" id="signup-step">
            <div class="signin-row">
                <a href="javascript:void(0);" onclick="showLogin()" class="step step-3" data-step="1">
                    <i class="fa fa-angle-left mr5" aria-hidden="true"></i>返回登入</a>
                <h3>註冊會員</h3>
                <fieldset>
                    <div class="form-group">
                        <?= Html::activeTextInput(
                            $signupModel,
                            'username',
                            [
                                'placeholder' => 'E-Mail',
                                'autofocus' => '1',
                                'class' => 'form-control input-lg',
                                'data-v-rule' => 'email',
                                'data-v-msg' => 'E-Mail格式不正確'
                            ]
                        ) ?>
                    </div>
                    <div class="form-group">
                        <?= Html::activePasswordInput(
                            $signupModel,
                            'password',
                            [
                                'placeholder' => '密碼',
                                'class' => 'form-control input-lg',
                                'data-v-rule' => '',
                                'data-v-msg' => '請輸入密碼'
                            ]
                        ) ?>
                    </div>
                    <div class="form-group">
                        <?= Html::activeTextInput(
                            $signupModel,
                            'name',
                            [
                                'placeholder' => '暱稱',
                                'class' => 'form-control input-lg',
                                'data-v-rule' => '',
                                'data-v-msg' => '請輸入暱稱'
                            ]
                        ) ?>
                    </div>
                    <div class="form-group">
                        <label for="<?= Html::getInputId($signupModel, 'area_id') ?>" class="sr-only">區域</label>
                        <?php
                        $areaOptions = ArrayHelper::merge(['' => '請選擇區域'], ArrayHelper::map($areaList, 'id', 'area_name'));
                        ?>
                        <?= Html::activeDropDownList(
                            $signupModel,
                            'area_id',
                            $areaOptions,
                            [
                                'class' => 'form-control input-lg',
                                'data-v-rule' => '',
                                'data-v-msg' => '請選擇區域'
                            ]
                        ) ?>
                    </div>
                    <?= HtmlHelper::displayFlash() ?>
                    <input class="btn btn-success btn-block mt15 mb10 btn-xlg" type="submit" value="註冊">
                </fieldset>
            </div>
        </div>
    </form>
    <!--會員註冊-結束-->
    <!--會員登入-結束-->
<?php endif ?>
<?php InlineScript::begin() ?>
<script>
    (function() {
        var fbCallback = function(response) {
            console.log(response);
            var uid = response.userID;
            var token = response.accessToken;
            var postData = {
                token: token,
                id: uid,
                '<?= yii::$app->request->csrfParam ?>': '<?= yii::$app->request->csrfToken ?>'
            };
            $.post('<?= Url::to("/member/xhr-fb-login") ?>', postData,
                function(data) {
                    console.log(data);
                    if (data.code === '000') {
                        alert(response.name + "您好，成功登入台灣獅子大學");
                        location.href = '/';
                    } else {
                        alert(data.message);
                        // window.history.back();
                    }
                }, 'json');


        };

        // 切換顯示登入/註冊表單
        window.showLogin = function() {
            $('#login-form').show();
            $('#signup-form').hide();
        };

        window.showSignup = function() {
            $('#login-form').hide();
            $('#signup-form').show();
        };

        $('#login-form').submit(function() {
            if ($(this).formValidate()) {
                $('input[type="submit"]').attr('disabled', 'disabled');
                return true;
            }
            return false;
        });

        $('#signup-form').submit(function() {
            var formParams = {
                '<?= Html::getInputName($signupModel, 'area_id') ?>': [function() {
                    var areaId = document.getElementById('<?= Html::getInputId($signupModel, 'area_id') ?>');
                    if (areaId.value && areaId.value !== '') {
                        return true;
                    } else {
                        return false;
                    }
                }, '請選擇區域']
            };
            if ($(this).formValidate(formParams)) {
                $('input[type="submit"]').attr('disabled', 'disabled');
                return true;
            }
            return false;
        });

        $('.js-fb-login').click(function() {
            common.facebook.login('<?php echo yii::$app->params["fbAppId"] ?>', 'email,public_profile', fbCallback);
        });
    })();
</script>
<?php InlineScript::end() ?>