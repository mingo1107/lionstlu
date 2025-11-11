<?php

use backend\widget\Breadcrumbs;
use ball\helper\HtmlHelper;
use ball\util\HttpUtil;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $list \common\models\AccessRoleModel[] */
/* @var $start int */
/* @var $count int */
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
                    <div>
                        <a href="/<?= Yii::$app->controller->id ?>/create"
                           class="btn btn-primary ">建立<?= $actionLabel ?></a>
                    </div>
                    <?php if (!empty($list)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                <tr>
                                    <th class="text-center" width="5%">
                                        <input type="checkbox" id="_select-all" name="_select-all" value="1"/>
                                    </th>
                                    <th class="text-center" width="30%">名稱</th>
                                    <th class="text-center" width="15%">建立時間 / 更新時間</th>
                                    <th class="text-center" width="10%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($list as $model):
                                    ?>
                                    <tr>
                                        <th class="text-center" width="5%">
                                            <input type="checkbox" id="_select-<?= $model->id ?>" name="_select[]"
                                                   value="<?= $model->id ?>"/>
                                        </th>
                                        <td class="text-center"><?= $model->name ?></td>
                                        <td class="text-center">
                                            <div><?= $model->create_time ?></div>
                                            <div><?= empty($model->update_time) ? '無' : $model->update_time ?></div>
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-general btn-block"
                                               href="/<?= Yii::$app->controller->id ?>/update<?= HttpUtil::buildQuery($_GET, [], ['id' => $model->id]) ?>">編輯</a>
                                            <a class="btn btn-outline-danger btn-block"
                                               onclick="return confirm('確認刪除?')"
                                               href="/<?= Yii::$app->controller->id ?>/delete<?= HttpUtil::buildQuery($_GET, [], ['id' => $model->id]) ?>">刪除</a>
                                        </td>
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
