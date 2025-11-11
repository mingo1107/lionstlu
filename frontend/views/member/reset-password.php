<?php

use backend\widget\InlineScript;
use frontend\assets\FormValidateAsset;
use frontend\models\MemberResetPasswordForm;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model MemberResetPasswordForm */
/* @var $token string */

FormValidateAsset::register($this);
?>
<?php if (yii::$app->user->isGuest): ?>
    <!--會員登入-開始-->
    <form id="#reset-password" name="#reset-password" method="post">
        <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
               value="<?= yii::$app->request->csrfToken ?>"/>
        <div class="modal-body step step-1 container">
            <div class="signin-row">
                <h3>密碼重置</h3>
                <fieldset>
                    <div class="row form-group register-password">
                        <label for="reset-password"
                               class="col-sm-2 col-form-label">新密碼</label>
                        <div class="col-sm-10">
                            <?php echo Html::activePasswordInput($model, 'password',
                                ['class' => 'form-control col-12', 'placeholder' => "請輸入新密碼",
                                ]) ?>
                        </div>
                    </div>
                    <div class="row form-group register-password">
                        <label for="reset-password2"
                               class="col-sm-2 col-form-label">再輸入一次新密碼</label>
                        <div class="col-sm-10">
                            <?php echo Html::activePasswordInput($model, 'password2',
                                ['class' => 'form-control col-12', 'placeholder' => "請再輸入一次新密碼",
                                ]) ?>
                        </div>
                    </div>
                    <?php echo Html::hiddenInput(Html::getInputName($model, 'token'), $token, ['id' => "reset-password-token"]) ?>
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

    var formParams = {
        '<?= Html::getInputName($model, 'password') ?>': [function () {
            var password = document.getElementById('<?= Html::getInputId($model, 'password') ?>');
            return $.trim(password.value).length >= 8;
        }, '密碼必須大於8個字元'],
        '<?= Html::getInputName($model, 'password2') ?>': [function () {
            var password = document.getElementById('<?= Html::getInputId($model, 'password') ?>');
            var password2 = document.getElementById('<?= Html::getInputId($model, 'password2') ?>');
            return $.trim(password.value).length > 0 && password.value === password2.value;
        }, '密碼必須一致'],
    };

    $('form').submit(function () {
        return $(this).formValidate(formParams);
    });
</script>
<?php InlineScript::end() ?>
