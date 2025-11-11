<?php

use backend\widget\InlineScript;
use ball\helper\HtmlHelper;
use frontend\assets\FormValidateAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \frontend\models\LoginForm */

FormValidateAsset::register($this);
?>
<?php if (yii::$app->user->isGuest): ?>
    <!--加入會員-開始-->
    <form id="main-form" name="main-form" method="post">
        <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
               value="<?= yii::$app->request->csrfToken ?>"/>
        <div class="modal-body step step-3 container" data-step="3">
            <div class="signin-row">
                <a href="/member/login" id="back" class="step step-3" data-step="1"
                   onClick="sendEvent('#demo-modal-1', 1)"><i
                            class="fa fa-angle-left mr5" aria-hidden="true"></i>返回登入</a>
                <h3>註冊會員</h3>
                <fieldset>
                    <button class="btn btn-block btn-fb btn-xlg" type="button"><i
                                class="fa fa-facebook-official mr10" aria-hidden="true"></i>facebook帳號繼續
                    </button>
                    <center>
                        <h4 style="color:#75787b">或</h4>
                    </center>
                    <div class="form-group">
                        <?= Html::activeTextInput($model, 'username',
                            ['placeholder' => 'E-Mail', 'autofocus' => '1', 'class' => 'form-control',
                                'data-v-rule' => 'email', 'data-v-msg' => 'E-Mail格式不正確']) ?>
                    </div>
                    <div class="form-group">
                        <?= Html::activePasswordInput($model, 'password',
                            ['placeholder' => '密碼', 'class' => 'form-control',
                                'data-v-rule' => '', 'data-v-msg' => '請輸入密碼']) ?>
                    </div>
                    <div class="form-group">
                        <?= Html::activeTextInput($model, 'name',
                            ['placeholder' => '暱稱', 'class' => 'form-control',
                                'data-v-rule' => '', 'data-v-msg' => '請輸入暱稱']) ?>
                    </div>
                    <?= HtmlHelper::displayFlash() ?>
                    <!--                <div class="checkbox">-->
                    <!--                    <label>-->
                    <!--                        <input type="checkbox" value="remember-me">-->
                    <!--                        我以閱讀並同意<a href="#">會員條款</a> </label>-->
                    <!--                </div>-->
                    <input class="btn btn-success btn-block mt15 mb10 btn-xlg" type="submit" value="註冊">
                </fieldset>
            </div>
        </div>
    </form>
    <!--加入會員-結束-->
<?php endif ?>
<?php InlineScript::begin() ?>
<script>
    <?php if(!yii::$app->user->isGuest):?>
    alert('會員註冊成功');
    parent.jQuery.fancybox.getInstance().close();
    parent.location.reload();
    <?php endif;?>
    (function () {
        $('#main-form').submit(function () {
            if ($(this).formValidate()) {
                $('input[type="submit"]').attr('disabled', 'disabled');
                return true;
            }
            return false;
        });
    })();
</script>
<?php InlineScript::end() ?>
