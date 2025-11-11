<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use backend\widget\NavTab;
use ball\helper\HtmlHelper;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\VoteModel */
/* @var $list \common\models\VoteOptionModel[] */

FormValidateAsset::register($this);
$index = 0;
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
                        <h5><?= $model->name ?></h5>
                    </div>
                    <div class="ibox-content">
                        <?= NavTab::widget([
                            'links' => [
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/update?id=" . $model->id,
                                    'label' => $actionLabel . "資訊"
                                ],
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/option?id=" . $model->id,
                                    'label' => $actionLabel . "選項",
                                    'active' => true
                                ],
                                [
                                    'url' => "/" . Yii::$app->controller->id . "/record?id=" . $model->id,
                                    'label' => $actionLabel . "統計",
                                ]
                            ]
                        ]) ?>
                        <?= HtmlHelper::displayFlash() ?>
                        <?php if (empty($list)): ?>
                            <div>
                                <a href="#" class="btn btn-primary js-void js-option">新增選項</a>
                            </div>
                        <?php endif ?>
                        <form id="main-form" name="main-form" class="form-horizontal" method="post" action="<?= $qs ?>">
                            <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
                                   value="<?= yii::$app->request->csrfToken ?>"/>
                            <input type="hidden" name="update" value="1"/>
                            <div id="option-div">
                                <?php foreach ($list as $option): ?>
                                    <div class="form-group">
                                        <label for="option-<?= $option->id ?>"
                                               class="col-sm-2 control-label">選項內容</label>
                                        <div class="col-sm-10">
                                            <input type="text" id="option-<?= $option->id ?>" class="form-control"
                                                   name="option-<?= $option->id ?>" value="<?= $option->name ?>"
                                                   required/>
                                        </div>
                                    </div>
                                    <?php
                                    ++$index;
                                endforeach; ?>
                            </div>
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
    <!-- option div template -->
    <div id="template" class="hidden">
        <div class="form-group" id="form-option-%index%">
            <label for="%index%" class="col-sm-2 control-label">選項內容</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input type="text" id="option-%index%" class="form-control" name="option[]" value=""
                           placeholder="請輸入選項內容" maxlength="512" required/>
                    <span class="input-group-addon js-delete" data-id="form-option-%index%">
                        <i class="glyphicon glyphicon-trash"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
<?php InlineScript::begin() ?>
    <script>
        (function (common) {
            var index = <?=$index?>;
            var $template = $('#template');
            $('.js-option').click(function () {
                var html = $template.clone().html();
                $('#option-div').append(common.replaceAll(html, '%index%', index++));

                $('.js-delete').click(function () {
                    var id = $(this).attr('data-id');
                    $('#' + id).remove();
                });
            });
        })(window.common);
    </script>
<?php InlineScript::end() ?>