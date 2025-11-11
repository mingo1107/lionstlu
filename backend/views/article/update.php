<?php

use backend\assets\FormValidateAsset;
use backend\widget\Breadcrumbs;
use backend\widget\CKEditor;
use backend\widget\InlineScript;
use backend\widget\Upload;
use ball\helper\File;
use ball\helper\HtmlHelper;
use common\models\ArticleModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $model \common\models\ArticleModel */
/* @var $mediaList \common\models\Media[] */
/* @var $coverMedia \common\models\Media */
/* @var $item \common\models\ProductModel|\common\models\VoteModel */
/* @var $categoryList \common\models\ArticleCategoryModel[] */

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
                            <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
                                   value="<?= yii::$app->request->csrfToken ?>"/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">文章分類</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'category_id', ArrayHelper::merge(['' => '請選擇'], ArrayHelper::map($categoryList, 'id', 'name')),
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇文章分類']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">狀態</label>
                                <div class="col-sm-10">
                                    <?php
                                        if($roleAuthority=='COLUMNIST'){
                                            echo Html::activeDropDownList($model, 'status', ['0' => '待審核'],
                                                ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']);

                                        }else{
                                            echo Html::activeDropDownList($model, 'status', ArrayHelper::merge(['' => '請選擇'], ArticleModel::$statusLabel),
                                                ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇狀態']);
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'name') ?>"
                                       class="col-sm-2 control-label">標題<br>og:title</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'title',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請填入標題']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'og_keywords') ?>"
                                       class="col-sm-2 control-label">og:keywords</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'og_keywords',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => 'og keywords']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'og_description') ?>"
                                       class="col-sm-2 control-label">og:description</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextarea($model, 'og_description',
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => 'og description']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">上架期限</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'deadline', ArticleModel::$deadlineLabel,
                                        ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇期限類型']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div id="deadline-interval"
                                 class="<?= $model->deadline == ArticleModel::DEADLINE_OFF ? 'hidden' : '' ?>">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">時間區間</label>
                                    <div class="col-sm-10">
                                        <div class="input-group date margin-1">
                                            <?= Html::activeTextInput($model, 'start_time',
                                                ['class' => 'form-control', 'placeholder' => '開始時間']) ?>
                                            <span class="input-group-addon">
                                                <i class="glyphicon glyphicon-calendar"></i>
                                            </span>
                                        </div>
                                        <div class="input-group date margin-1">
                                            <?= Html::activeTextInput($model, 'end_time',
                                                ['class' => 'form-control', 'placeholder' => '結束時間']) ?>
                                            <span class="input-group-addon">
                                                <i class="glyphicon glyphicon-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'sort') ?>"
                                       class="col-sm-2 control-label">排序</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'sort',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '排序必須為數字', 'value' => '0']) ?>
                                    <span class="help-block m-b-none">數值愈大排序愈前面</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <?php if($roleAuthority=='MANAGER'){ ?>
                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'views') ?>"
                                       class="col-sm-2 control-label">瀏覽數</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'views',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '必須為數字']) ?>
                                    <span class="help-block m-b-none">注意，前台會同步更新</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'share_count') ?>"
                                       class="col-sm-2 control-label">分享數</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'share_count',
                                        ['class' => 'form-control', 'data-v-rule' => 'digit', 'data-v-msg' => '必須為數字']) ?>
                                    <span class="help-block m-b-none">注意，前台會同步更新</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'share_location') ?>"
                                       class="col-sm-2 control-label">分享地區</label>
                                <div class="col-sm-10">
                                    <?= Html::activeTextInput($model, 'share_location',
                                        ['class' => 'form-control']) ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <?php } ?>


                            <div class="form-group">
                                <label for="<?= Html::getInputId($model, 'cover_media') ?>"
                                       class="col-sm-2 control-label">文章封面圖，顯示於列表<br>og:image</label>
                                <div class="col-sm-10">
                                    <?= Upload::widget([
                                        'id' => $model->getMediaInputName('cover_media'),
                                        'name' => $model->getMediaInputName('cover_media'),
                                        'category' => File::CATEGORY_ARTICLE,
                                        'current' => $coverMedia->src,
                                        'crop' => true,
                                    ]); ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <?php for ($i = 0; $i < $model->mediaAttribute['media']['size']; ++$i): ?>
                                <div class="form-group">
                                    <label for="<?= Html::getInputId($model, 'media') ?>"
                                           class="col-sm-2 control-label">文章圖片<?= $i + 1 ?></label>
                                    <div class="col-sm-10">
                                        <?= Upload::widget([
                                            'id' => $model->getMediaInputName('media', "src", $i),
                                            'name' => $model->getMediaInputName('media', "src", $i),
                                            'category' => File::CATEGORY_ARTICLE,
                                            'current' => $mediaList[$i]->src
                                        ]); ?>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label for="<?= Html::getInputId($model, 'intro') ?>"
                                           class="col-sm-2 control-label">文章內容<?= $i + 1 ?></label>
                                    <div class="col-sm-10">
                                        <?= CKEditor::widget([
                                            'model' => $model,
                                            'attribute' => "content_$i",
                                            'options' => ['class' => 'form-control']
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            <?php endfor; ?>

                        </div>
                    </div>
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>促銷廣告</h5>
                        </div>
                        <div class="ibox-content">
                            <?= Html::activeHiddenInput($model, 'ad_id') ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">廣告類型</label>
                                <div class="col-sm-10">
                                    <?= Html::activeDropDownList($model, 'ad_type', ArticleModel::$adLabel,
                                        ['class' => 'form-control']) ?>
                                    <span id="ad-item" class="help-block m-b-none
                                    <?= $model->ad_type == ArticleModel::AD_PRODUCT || $model->ad_type == ArticleModel::AD_VOTE ? '' : 'hidden' ?>">
                                        <a id="ad-item-select" data-type="iframe" data-fancybox
                                           href="select?type=<?= $model->ad_type ?>">選擇物件</a>
                                    </span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div id="ad-deadline"
                                 class="<?= $model->ad_type == ArticleModel::AD_NONE ? 'hidden' : '' ?>">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">廣告期限類型</label>
                                    <div class="col-sm-10">
                                        <?= Html::activeDropDownList($model, 'ad_deadline', ArticleModel::$deadlineLabel,
                                            ['class' => 'form-control', 'data-v-rule' => '', 'data-v-msg' => '請選擇期限類型']) ?>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            <div id="ad-deadline-interval"
                                 class="<?= $model->ad_deadline == ArticleModel::DEADLINE_OFF ? 'hidden' : '' ?>">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">廣告時間區間</label>
                                    <div class="col-sm-10">
                                        <div class="input-group date margin-1">
                                            <?= Html::activeTextInput($model, 'ad_start_time',
                                                ['class' => 'form-control', 'placeholder' => '開始時間']) ?>
                                            <span class="input-group-addon">
                                                <i class="glyphicon glyphicon-calendar"></i>
                                            </span>
                                        </div>
                                        <div class="input-group date margin-1">
                                            <?= Html::activeTextInput($model, 'ad_end_time',
                                                ['class' => 'form-control', 'placeholder' => '結束時間']) ?>
                                            <span class="input-group-addon">
                                                <i class="glyphicon glyphicon-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>
                            <div id="ad-item-info"
                                 class="<?= $model->ad_type == ArticleModel::AD_PRODUCT || $model->ad_type == ArticleModel::AD_VOTE ? '' : 'hidden' ?>">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">已選擇物件</label>
                                    <div class="col-lg-10">
                                        <p id="ad-item-detail"
                                           class="form-control-static"><?= !empty($item) ? $item->name : '無' ?></p>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>
                            <div id="ad-link" class="<?= $model->ad_type == ArticleModel::AD_LINK ? '' : 'hidden' ?>">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">廣告連結</label>
                                    <div class="col-sm-10">
                                        <?= Html::activeTextInput($model, 'ad_link',
                                            ['class' => 'form-control']) ?>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
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
            var formParams = {};
            var deadlineInterval = document.getElementById('deadline-interval');
            var adDeadline = document.getElementById('ad-deadline');
            var adDeadlineInterval = document.getElementById('ad-deadline-interval');
            var adLink = document.getElementById('ad-link');
            var adItem = document.getElementById('ad-item');
            var adItemLink = document.getElementById('ad-item-select');
            var adItemInfo = document.getElementById('ad-item-info');

            $('#<?=Html::getInputId($model, 'deadline')?>').change(function () {
                if (this.value === '<?=ArticleModel::DEADLINE_ON?>') {
                    deadlineInterval.classList.remove('hidden');
                    formParams['<?=Html::getInputName($model, "start_time")?>'] = ['', '請輸入開始時間'];
                    formParams['<?=Html::getInputName($model, "end_time")?>'] = ['', '請輸入結束時間'];
                } else if (this.value === '<?=ArticleModel::DEADLINE_OFF?>') {
                    deadlineInterval.classList.add('hidden');
                    delete formParams['<?=Html::getInputName($model, "start_time")?>'];
                    delete formParams['<?=Html::getInputName($model, "end_time")?>'];
                }
            });

            $('#<?=Html::getInputId($model, 'ad_deadline')?>').change(function () {
                if (this.value === '<?=ArticleModel::DEADLINE_ON?>') {
                    adDeadlineInterval.classList.remove('hidden');
                    formParams['<?=Html::getInputName($model, "ad_start_time")?>'] = ['', '請輸入開始時間'];
                    formParams['<?=Html::getInputName($model, "ad_end_time")?>'] = ['', '請輸入結束時間'];
                } else if (this.value === '<?=ArticleModel::DEADLINE_OFF?>') {
                    adDeadlineInterval.classList.add('hidden');
                    delete formParams['<?=Html::getInputName($model, "ad_start_time")?>'];
                    delete formParams['<?=Html::getInputName($model, "ad_end_time")?>'];
                }
            });

            $('#<?=Html::getInputId($model, 'ad_type')?>').change(function () {
                switch (this.value) {
                    case '<?=ArticleModel::AD_NONE?>':
                        adDeadline.classList.add('hidden');
                        adLink.classList.add('hidden');
                        adItem.classList.add('hidden');
                        adItemInfo.classList.add('hidden');
                        $('#<?=Html::getInputId($model, 'ad_link')?>').val('');
                        break;
                    case '<?=ArticleModel::AD_LINK?>':
                        adDeadline.classList.remove('hidden');
                        adLink.classList.remove('hidden');
                        adItem.classList.add('hidden');
                        adItemInfo.classList.add('hidden');
                        break;
                    case '<?=ArticleModel::AD_PRODUCT?>':
                    case '<?=ArticleModel::AD_VOTE?>':
                        adDeadline.classList.remove('hidden');
                        adLink.classList.add('hidden');
                        $('#<?=Html::getInputId($model, 'ad_link')?>').val('');
                        adItem.classList.remove('hidden');
                        adItemInfo.classList.remove('hidden');
                        $(adItemLink).attr('href', 'select?type=' + this.value);
                        $.fancybox.open({
                            src: 'select?type=' + this.value,
                            type: 'iframe',
                            opts: {
                                afterShow: function (instance, current) {
                                    //console.info( 'done!' );
                                }
                            }
                        });
                        break;
                }
            });

            $('#main-form').submit(function () {
                return $(this).formValidate(formParams);
            });
        })();
    </script>
<?php InlineScript::end() ?>
