<?php

use backend\widget\InlineScript;
use common\models\ArticleModel;
use common\models\VoteModel;

/* @var $this \yii\web\View */
/* @var $list \common\models\VoteModel[] */
/* @var $vote \common\models\VoteModel */
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($vote)): ?>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>已選擇投票活動</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                <tr>
                                    <th class="text-center" width="5%">
                                    </th>
                                    <th class="text-center" width="30%">名稱</th>
                                    <th class="text-center" width="8%">狀態</th>
                                    <th class="text-center" width="30%">活動時間</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($vote->deadline == 1) {
                                    $interval = $vote->start_time . '<br/>' . $vote->end_time;
                                } else {
                                    $interval = '永遠';
                                }
                                ?>
                                <tr>
                                    <th class="text-center" width="5%">
                                        <input type="checkbox" class="js-select" id="_select-<?= $vote->id ?>"
                                               name="item[]"
                                               value="<?= $vote->id ?>"/>
                                    </th>
                                    <td class="text-center"><?= $vote->name ?></td>
                                    <td class="text-center"><?= VoteModel::$statusLabel[$vote->status] ?></td>
                                    <td class="text-center"><?= $interval ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>請選擇投票活動</h5>
                </div>
                <div class="ibox-content">
                    <div>
                        <a href="#" id="select-submit"
                           class="btn btn-primary js-void">送出選擇</a>
                    </div>
                    <?php if (!empty($list)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                <tr>
                                    <th class="text-center" width="5%">
                                    </th>
                                    <th class="text-center" width="30%">名稱</th>
                                    <th class="text-center" width="8%">狀態</th>
                                    <th class="text-center" width="30%">活動時間</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($list as $model):
                                    if ($model->deadline == 1) {
                                        $interval = $model->start_time . '<br/>' . $model->end_time;
                                    } else {
                                        $interval = '永遠';
                                    }
                                    ?>
                                    <tr>
                                        <th class="text-center" width="5%">
                                            <input type="checkbox" class="js-select" id="_select-<?= $model->id ?>"
                                                   name="item[]"
                                                   value="<?= $model->id ?>"/>
                                        </th>
                                        <td class="text-center"><?= $model->name ?></td>
                                        <td class="text-center"><?= VoteModel::$statusLabel[$model->status] ?></td>
                                        <td class="text-center"><?= $interval ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">目前無資料</div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php InlineScript::begin() ?>
<script>
    (function (pjq, $) {
        var id = undefined;
        $('.js-select').click(function () {
            $('input[name^="item"').each(function () {
                this.checked = false;
            });
            this.checked = true;
            id = this.value;
        });

        $('#select-submit').click(function () {
            if (typeof id === 'undefined') {
                alert('請選擇投票活動');
                return false;
            }
            var params = {
                '<?=Yii::$app->request->csrfParam?>': '<?=Yii::$app->request->csrfToken?>',
                'type': '<?=ArticleModel::AD_VOTE?>',
                'id': id
            };
            $.post('xhr-select', params, function (data) {
                if (!data.error) {
                    window.parent.document.getElementById('articlemodel-ad_id').value = data.id;
                    window.parent.document.getElementById('ad-item-detail').innerHTML = data.name;
                    window.parent.$.fancybox.close();
                } else {
                    alert(data.error.message);
                }
            }, 'json');
        })
    })(window.parent.jQuery, jQuery);
</script>
<?php InlineScript::end() ?>
