<?php

use backend\widget\Breadcrumbs;
use ball\helper\HtmlHelper;
use ball\util\HttpUtil;
use common\models\UserModel;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $list \common\models\UserModel[] */
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
                                    <th class="text-center" width="10%">類型</th>
                                    <th class="text-center" width="10%">權限群組</th>
                                    <th class="text-center" width="15%">ID</th>
                                    <th class="text-center" width="20%">名稱</th>
                                    <th class="text-center" width="8%">狀態</th>
                                    <th class="text-center" width="8%">登入次數</th>
                                    <th class="text-center" width="8%">最後登入時間</th>
                                    <th class="text-center" width="10%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($list as $model): ?>
                                    <tr>
                                        <th class="text-center" width="5%">
                                            <input type="checkbox" id="_select-<?= $model->id ?>" name="_select[]"
                                                   value="<?= $model->id ?>"/>
                                        </th>
                                        <td class="text-center"><?= UserModel::$roleLabel[$model->role] ?></td>
                                        <td class="text-center"><?= !empty($model->role_name) ? $model->role_name : '無' ?></td>
                                        <td class="text-center"><?= $model->username ?></td>
                                        <td class="text-center"><?= $model->name ?></td>
                                        <td class="text-center"><?= UserModel::$statusLabel[$model->status] ?></td>
                                        <td class="text-center"><?= $model->login_count ?></td>
                                        <td class="text-center"><?= $model->last_login_time ?></td>
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
