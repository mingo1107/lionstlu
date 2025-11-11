<?php

use backend\widget\InlineScript;
use common\models\ArticleModel;
use common\models\ProductModel;

/* @var $this \yii\web\View */
/* @var $list \common\models\ProductModel[] */
/* @var $product \common\models\ProductModel */
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($product)): ?>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>已選擇商品</h5>
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
                                    <th class="text-center" width="30%">規格類型</th>
                                    <th class="text-center" width="30%">上架時間</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($product->deadline == 1) {
                                    $interval = $product->start_time . '<br/>' . $product->end_time;
                                } else {
                                    $interval = '永遠';
                                }
                                ?>
                                <tr>
                                    <th class="text-center" width="5%">
                                        <input type="checkbox" class="js-select" id="_select-<?= $product->id ?>"
                                               name="item[]"
                                               value="<?= $product->id ?>"/>
                                    </th>
                                    <td class="text-center"><?= $product->name ?></td>
                                    <td class="text-center"><?= ProductModel::$statusLabel[$product->status] ?></td>
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
                    <h5>請選擇商品</h5>
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
                                    <th class="text-center" width="30%">規格類型</th>
                                    <th class="text-center" width="30%">上架時間</th>
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
                                        <td class="text-center"><?= ProductModel::$statusLabel[$model->status] ?></td>
                                        <td class="text-center"><?= ProductModel::$standardTypeLabel[$model->standard_type] ?></td>
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
                alert('請選擇商品');
                return false;
            }
            var params = {
                '<?=yii::$app->request->csrfParam?>': '<?=yii::$app->request->csrfToken?>',
                'type': '<?=ArticleModel::AD_PRODUCT?>',
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
