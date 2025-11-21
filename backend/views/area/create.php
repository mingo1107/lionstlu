<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use ball\helper\HtmlHelper;
use common\models\AreaModel;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\AreaModel */

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
                        <h5><?= $title ?></h5>
                    </div>
                    <div class="ibox-content">
                        <?= HtmlHelper::displayFlash() ?>
                        <form id="main-form" name="main-form" class="form-horizontal" method="post" action="<?= $qs ?>">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                   value="<?= Yii::$app->request->csrfToken ?>"/>
                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'area_name') ?>"
                                       class="col-sm-2 control-label">區域名稱</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'area_name',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入區域名稱']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">排序</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'sort',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '排序必須為數字', 'value' => '0']) ?>
                                    <span class="help-block m-b-none">數值愈大排序愈前面</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary mr10">送出</button>

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
            $('#main-form').submit(function () {
                return $(this).formValidate();
            });
        })();
    </script>
<?php InlineScript::end() ?>

