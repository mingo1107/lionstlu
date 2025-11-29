<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use ball\helper\HtmlHelper;
use common\models\QuickLink;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\QuickLink */

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
            <form id="main-form" name="main-form" class="form-horizontal" method="post" action="<?= $qs ?>">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= $title ?></h5>
                    </div>
                    <div class="ibox-content">
                        <?= HtmlHelper::displayFlash() ?>
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                               value="<?= Yii::$app->request->csrfToken ?>"/>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'title') ?>"
                                   class="col-sm-2 control-label">標題</label>
                            <div class="col-sm-10">
                                <?= Html::activeTextInput($model, 'title',
                                    ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入標題']) ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'url') ?>"
                                   class="col-sm-2 control-label">連結</label>
                            <div class="col-sm-10">
                                <?= Html::activeTextInput($model, 'url',
                                    ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入連結', 'placeholder' => 'https://example.com']) ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'icon') ?>"
                                   class="col-sm-2 control-label">圖示</label>
                            <div class="col-sm-10">
                                <?= Html::activeTextInput($model, 'icon',
                                    ['class' => 'form-control', 'placeholder' => '/images/icon1.png']) ?>
                                <span class="help-block m-b-none">請輸入圖示路徑，例如：/images/icon1.png</span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label for="<?= Html::getInputId($model, 'sort') ?>"
                                   class="col-sm-2 control-label">排序</label>
                            <div class="col-sm-10">
                                <?= Html::activeTextInput($model, 'sort',
                                    ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '排序必須為數字', 'value' => '0']) ?>
                                <span class="help-block m-b-none">數值愈小排序愈前面</span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">權限限制</label>
                            <div class="col-sm-10">
                                <?= Html::activeDropDownList($model, 'is_login', QuickLink::$isLoginLabel,
                                    ['class' => 'form-control']) ?>
                                <span class="help-block m-b-none">「需登入」：只有登入會員才能看到此連結</span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">狀態</label>
                            <div class="col-sm-10">
                                <?= Html::activeDropDownList($model, 'status', ArrayHelper::merge(['' => '請選擇'], QuickLink::$statusLabel),
                                    ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']) ?>
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
    (function () {
        var formParams = {};
        $('#main-form').submit(function () {
            return $(this).formValidate(formParams);
        });
    })();
</script>
<?php InlineScript::end() ?>
