<?php

use backend\widget\Breadcrumbs;
use ball\helper\HtmlHelper;
use ball\util\HttpUtil;
use common\models\CustomerServiceModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $list \common\models\CustomerServiceModel[] */
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
    <div class="col-lg-2">
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <form class="navbar-form navbar-left" role="search">
            <div class="form-group">
                <?= Html::dropDownList('category', yii::$app->request->get("category"),
                    ArrayHelper::merge(["" => "全部"], CustomerServiceModel::$categoryLabel),
                    ['class' => 'form-control']) ?>
            </div>
            <button type="submit" class="btn btn-default">搜尋</button>
        </form>
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= $title ?></h5>
                </div>
                <div class="ibox-content">
                    <?= HtmlHelper::displayFlash() ?>
                    <?php if (!empty($list)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                <tr>
                                    <th class="text-center" width="5%">
                                        <input type="checkbox" id="_select-all" name="_select-all" value="1"/>
                                    </th>
                                    <th class="text-center" width="30%">標題</th>
                                    <th class="text-center" width="8%">分類</th>
                                    <th class="text-center" width="8%">狀態</th>
                                    <th class="text-center" width="15%">建立時間</th>
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
                                        <td class="text-center"><?= $model->title ?></td>
                                        <td class="text-center"><?= isset(CustomerServiceModel::$categoryLabel[$model->category]) ? CustomerServiceModel::$categoryLabel[$model->category] : '無' ?></td>
                                        <td class="text-center"><?= isset(CustomerServiceModel::$statusLabel[$model->status]) ? CustomerServiceModel::$statusLabel[$model->status] : '無' ?></td>
                                        <td class="text-center">
                                            <?= $model->create_time ?>
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
