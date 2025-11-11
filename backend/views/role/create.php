<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use ball\helper\HtmlHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\AccessRoleModel */
/* @var $roleList \common\models\AccessRoleModel[] */
$menuParentList = yii::$app->view->params['menuParentList'];
/* @var $menuList \common\models\AccessModel[] */
$menuList = yii::$app->view->params['menuList'];
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
                                <?= Html::activeTextInput($model, 'name',
                                    ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入名稱',
                                        'placeholder' => '請輸入群組名稱']) ?>
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                    <?php foreach ($menuParentList as $parentMenu): ?>
                                        <ul class="list-group" id="ul-<?= $parentMenu->id ?>">
                                            <li class="list-group-item">
                                                <input type="checkbox" class="js-parent"
                                                       id="access-<?= $parentMenu->id ?>"
                                                       name="access[]"
                                                       value="<?= $parentMenu->id ?>">
                                                <label for="access-<?= $parentMenu->id ?>">
                                                    <?= $parentMenu->name ?>
                                                </label>
                                            </li>
                                            <?php foreach ($menuList[$parentMenu->id] as $menu): ?>
                                                <li class="list-group-item">
                                                    <span class="indent" style="padding-left: 2em"></span>
                                                    <input type="checkbox" id="access-<?= $menu->id ?>" name="access[]"
                                                           value="<?= $menu->id ?>">
                                                    <label for="access-<?= $menu->id ?>"><?= $menu->name ?></label>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endforeach; ?>
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

            $('.js-parent').click(function () {
                var $li = $('#ul-' + this.value).find('input:checkbox');
                var apply = function (check) {
                    $li.each(function () {
                        this.checked = check;
                    });
                };
                if (this.checked) {
                    apply(true);
                } else {
                    apply(false);
                }
            });
        })();
    </script>
<?php InlineScript::end() ?>