<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use backend\widget\Upload;
use ball\helper\HtmlHelper;
use common\models\BannerModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\BannerModel */
/* @var $media \common\models\Media */
/* @var $mediaM \common\models\Media */

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
                            <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
                                   value="<?= yii::$app->request->csrfToken ?>"/>

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
                                <label for="<?= Html::getInputId($model, 'name') ?>"
                                       class="col-sm-2 control-label">名稱</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'name',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入名稱']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'link') ?>"
                                       class="col-sm-2 control-label">連結</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'link',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入連結']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">狀態</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'status', ArrayHelper::merge(['' => '請選擇'], BannerModel::$statusLabel),
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">排序</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'sort',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '排序必須為數字']) ?>
                                    <span class="help-block m-b-none">數值愈大排序愈前面</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'media') ?>"
                                       class="col-sm-2 control-label">網站版廣告圖片</label>
                                <div class="col-sm-10">
                                    <?= Upload::widget([
                                        'id' => $model->getMediaInputName('media'),
                                        'name' => $model->getMediaInputName('media'),
                                        'category' => $model->mediaAttribute['media']['category'],
                                        'current' => $media->src,
                                        'crop' => true,
                                        'cropRatio' => 3 / 1
                                    ]); ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'media_m') ?>"
                                       class="col-sm-2 control-label">手機板廣告圖片</label>
                                <div class="col-sm-10">
                                    <?= Upload::widget([
                                        'id' => $model->getMediaInputName('media_m'),
                                        'name' => $model->getMediaInputName('media_m'),
                                        'category' => $model->mediaAttribute['media_m']['category'],
                                        'crop' => true,
                                        'cropRatio' => 1,
                                        'current' => $mediaM->src
                                    ]); ?>
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
                var params = {
                    '<?=$model->getMediaInputName('media')?>': ['', '請上傳網站版廣告圖片'],
                    '<?=$model->getMediaInputName('media_m')?>': ['', '請上傳手機版廣告圖片']
                };
                return $(this).formValidate(params);
            });
        })();
    </script>
<?php InlineScript::end() ?>