<?php

use backend\widget\InlineScript;
use ball\helper\HtmlHelper;
use common\models\CustomerServiceModel;
use frontend\assets\FormValidateAsset;
use yii\helpers\Html;

/* @var $category int */
/* @var $model common\models\CustomerServiceModel */
FormValidateAsset::register($this);
?>
<!--內容_開始-->
<div class="container-700">
    <div class="row">
        <div class="col-md-12 other-col">
            <h2>異業合作，採訪邀約，我要投稿</h2>
            <p>填寫以下您預詢問的內容，客服人員將儘速為您處理</p>
        </div>
        <div class="col-md-12 m-2">
            <?= HtmlHelper::displayFlash() ?>
            <form id="cs-form" name="cs-form" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                       value="<?= Yii::$app->request->csrfToken ?>"/>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">提問類型</label>
                    <?= Html::activeDropDownList($model, 'category', CustomerServiceModel::$categoryLabel,
                        ['class' => 'form-control']) ?>
                </div>

<!--                <div id="type" class="form-group hidden">-->
<!--                    <label for="inputEmail3" class="col-sm-2 control-label">訂單問題</label>-->
<!--                    -->
<!--                </div>-->

                <div class="form-group border-dashed">
                    <label for="title">提問主旨</label>
                    <?= Html::activeTextInput($model, 'title',
                        ['class' => 'form-control', 'placeholder' => '提問主旨',
                            'data-v-rule' => '', 'data-v-msg' => '請輸入提問主旨']) ?>
                </div>
                <div class="form-group">
                    <label for="name">您的姓名</label>
                    <?= Html::activeTextInput($model, 'name',
                        ['class' => 'form-control', 'placeholder' => '您的姓名',
                            'data-v-rule' => '', 'data-v-msg' => '請輸入您的姓名']) ?>
                </div>
                <div class="form-group">
                    <label for="email">提問主旨</label>
                    <?= Html::activeTextInput($model, 'email',
                        ['class' => 'form-control', 'placeholder' => 'E-Mail',
                            'data-v-rule' => '', 'data-v-msg' => '請輸入E-Mail']) ?>
                </div>
                <div class="form-group">
                    <label for="mobile">手機號碼</label>
                    <?= Html::activeTextInput($model, 'mobile',
                        ['class' => 'form-control', 'placeholder' => '手機號碼']) ?>
                </div>
                <div class="form-group">
                    <label for="inputPassword3">提問內容</label>
                    <?= Html::activeTextarea($model, 'content',
                        ['class' => 'form-control', 'placeholder' => '提問內容',
                            'data-v-rule' => '', 'data-v-msg' => '請輸入提問內容', 'rows' => '10']) ?>
                </div>
                <button type="submit" class="btn btn-success">送出</button>
            </form>
        </div>
    </div>
    <!--內容_結束-->
    <?php InlineScript::begin() ?>
    <script>
        (function () {
            $('#cs-form').submit(function () {
                return $(this).formValidate();
            });
        })();
    </script>
    <?php InlineScript::end() ?>

</div>
