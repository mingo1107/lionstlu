<?php

use backend\widget\InlineScript;
use ball\helper\HtmlHelper;
use frontend\assets\FormValidateAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \frontend\models\LoginForm */

FormValidateAsset::register($this);
?>
<?php if (Yii::$app->user->isGuest): ?>
    <!--會員登入-開始-->
    <form id="main-form" name="main-form" method="post">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
               value="<?= Yii::$app->request->csrfToken ?>"/>
        <div class="modal-body step step-1 container">
            <div class="signin-row">
                <a href="/member/login" id="back" class="step step-3" data-step="1"
                   onClick="sendEvent('#demo-modal-1', 1)"><i
                            class="fa fa-angle-left mr5" aria-hidden="true"></i>返回登入</a>
                <h3>忘記密碼</h3>
                <fieldset>
                    <div class="form-group">
                        <?= Html::textInput('id', '',
                            ['placeholder' => '請輸入您的會員帳號(E-Mail)', 'autofocus' => '1', 'class' => 'form-control input-lg',
                                'data-v-rule' => 'email', 'data-v-msg' => 'E-Mail格式不正確']) ?>
                    </div>
                    <button type="submit" class="btn btn-block btn-xlg btn-success-outline">送出
                    </button>
                </fieldset>
            </div>
        </div>
    </form>
    <!--會員登入-結束-->
<?php endif ?>
<?php InlineScript::begin() ?>
<script>
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
