<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\InlineScript;
use backend\widget\NavTab;
use ball\helper\HtmlHelper;
use common\assets\TwCityAsset;
use common\models\UserModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\UserModel */
/* @var $menuParentList \common\models\AccessModel[] */
/* @var $accessUser \common\models\AccessUserModel */
/* @var $roleList \common\models\AccessRoleModel[] */
$menuParentList = Yii::$app->view->params['menuParentList'];
/* @var $menuList \common\models\AccessModel[] */
$menuList = Yii::$app->view->params['menuList'];
FormValidateAsset::register($this);
TwCityAsset::register($this);
$accessList = json_decode($accessUser->access_list, true);
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
                            <?= NavTab::widget([
                                'links' => [
                                    [
                                        'url' => "/" . Yii::$app->controller->id . "/update?id=" . $model->id,
                                        'label' => $title,
                                        'active' => true
                                    ]
                                ]
                            ]) ?>
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                   value="<?= Yii::$app->request->csrfToken ?>"/>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">ID</label>
                                <div class="col-lg-10">
                                    <p class="form-control-static"><?= $model->username ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

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
                                <label class="col-sm-2 control-label">最後登入ip</label>
                                <div class="col-lg-10">
                                    <p class="form-control-static"><?= $model->last_login_ip ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">最後登入時間</label>
                                <div class="col-lg-10">
                                    <p class="form-control-static"><?= $model->last_login_time ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">登入次數</label>
                                <div class="col-lg-10">
                                    <p class="form-control-static"><?= $model->login_count ?></p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'password') ?>"
                                       class="col-sm-2 control-label">密碼</label>
                                <div class="col-sm-10">
                                    <?= Html::activePasswordInput($model, 'password',
                                        ['class' => 'form-control']) ?>
                                    <span class="help-block m-b-none">不修改留白即可</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'password2') ?>"
                                       class="col-sm-2 control-label">請再輸入一次密碼</label>
                                <div class="col-sm-10">
                                    <?= Html::activePasswordInput($model, 'password2',
                                        ['class' => 'form-control']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">狀態</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'status', ArrayHelper::merge(['' => '請選擇'], UserModel::$statusLabel),
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">身分</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'role', ArrayHelper::merge(['' => '請選擇'], UserModel::$roleLabel),
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇身分']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'email') ?>"
                                       class="col-sm-2 control-label">E-mail</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'email',
                                        ['class' => 'form-control', 'data-v-rule' => 'email', 'data-v-msg' => 'Email格式不正確']) ?>
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
                                <label for="<?= Html::getInputId($model, 'mobile') ?>"
                                       class="col-sm-2 control-label">手機</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'mobile',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入手機']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'birthday') ?>"
                                       class="col-sm-2 control-label">生日</label>
                                <div class="input-group date col-sm-10">
                                    <?= Html::activeTextInput($model, 'birthday',
                                        ['class' => 'form-control']) ?>
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div id="city-selector">
                                    <label class="col-sm-2 control-label">所在城市</label>
                                    <div class="col-sm-10 margin-bottom-2">
                                        <?= Html::activeDropDownList($model, 'city', [],
                                            ['class' => 'form-control']) ?>
                                    </div>
                                    <label class="col-sm-2 control-label">所在地區</label>
                                    <div class="col-sm-10 margin-bottom-2">
                                        <?= Html::activeDropDownList($model, 'district', [],
                                            ['class' => 'form-control']) ?>
                                    </div>
                                    <?= Html::activeHiddenInput($model, 'zip') ?>
                                </div>
                                <label class="col-sm-2 control-label">所在地址</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'address',
                                        ['class' => 'form-control', 'placeholder' => '請輸入地址']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </div>
                    </div>
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>權限設定</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group">
                                <?= Html::activeDropDownList($accessUser, 'role_id',
                                    ArrayHelper::merge(['0' => '請選擇權限群組'], ArrayHelper::map($roleList, 'id', 'name')),
                                    ['class' => 'form-control']) ?>
                            </div>
                            <div class="form-group">
                                <?php foreach ($menuParentList as $parentMenu): ?>
                                    <ul class="list-group" id="ul-<?= $parentMenu->id ?>">
                                        <li class="list-group-item">
                                            <input type="checkbox" class="js-parent" id="access-<?= $parentMenu->id ?>"
                                                   name="access[]"
                                                   value="<?= $parentMenu->id ?>"
                                                <?= isset($accessList[$parentMenu->id]) ? 'checked="checked"' : '' ?>/>
                                            <label for="access-<?= $parentMenu->id ?>">
                                                <?= $parentMenu->name ?>
                                            </label>
                                        </li>
                                        <?php foreach ($menuList[$parentMenu->id] as $menu): ?>
                                            <li class="list-group-item">
                                                <span class="indent" style="padding-left: 2em"></span>
                                                <input type="checkbox" id="access-<?= $menu->id ?>" name="access[]"
                                                       value="<?= $menu->id ?>"
                                                    <?= isset($accessList[$menu->id]) ? 'checked="checked"' : '' ?>/>
                                                <label for="access-<?= $menu->id ?>"><?= $menu->name ?></label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endforeach; ?>
                            </div>
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
            var formParams = {
                '<?= Html::getInputName($model, 'password') ?>': [function () {
                    var password = document.getElementById('<?= Html::getInputId($model, 'password') ?>');
                    var password2 = document.getElementById('<?= Html::getInputId($model, 'password2') ?>');
                    if ($.trim(password.value).length == 0 ||
                        ($.trim(password.value).length >= 8 && password.value === password2.value)) {
                        return true;
                    } else {
                        return false;
                    }
                }, '密碼長度必須大於8個字元而且密碼必須一致']
            };

            $('#main-form').submit(function () {
                return $(this).formValidate(formParams);
            });

            new TwCitySelector({
                el: "#city-selector",
                elCounty: "#<?=Html::getInputId($model, 'city')?>", // 在 el 裡查找 dom
                elDistrict: "#<?=Html::getInputId($model, 'district')?>", // 在 el 裡查找 dom
                elZipcode: "#<?=Html::getInputId($model, 'zip')?>", // 在 el 裡查找 dom
                selectedCounty: '<?=$model->country?>',
                selectedDistrict: '<?=$model->city?>',
                countyClassName: "form-control margin-bottom-2",
                countyFiledName: "<?=Html::getInputName($model, 'city')?>",
                districtClassName: "form-control margin-bottom-2",
                districtFieldName: "<?=Html::getInputName($model, 'district')?>",
                zipcodeFiledName: "<?=Html::getInputName($model, 'zip')?>",
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