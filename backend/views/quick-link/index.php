<?php

use backend\widget\Breadcrumbs;
use backend\widget\Paging;
use ball\helper\HtmlHelper;
use ball\util\HttpUtil;
use common\models\QuickLink;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $list \common\models\QuickLink[] */
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
        <form class="navbar-form navbar-left" role="search">
            <div class="form-group">
                <?= Html::dropDownList('status', Yii::$app->request->get("status"),
                    ArrayHelper::merge(["" => "狀態"], QuickLink::$statusLabel),
                    ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::dropDownList('is_login', Yii::$app->request->get("is_login"),
                    ArrayHelper::merge(["" => "權限限制"], QuickLink::$isLoginLabel),
                    ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::textInput('keyword', Yii::$app->request->get("keyword"),
                    ['class' => 'form-control', 'placeholder' => '搜尋標題']) ?>
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
                    <div>
                        <!-- <a href="/<?= Yii::$app->controller->id ?>/create"
                           class="btn btn-primary ">建立<?= $actionLabel ?></a> -->
                    </div>
                    <?php if (!empty($list)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                <tr>
                                    <th class="text-center" width="5%">ID</th>
                                    <!-- <th class="text-center" width="10%">圖示</th> -->
                                    <th class="text-center" width="15%">標題</th>
                                    <th class="text-center" width="25%">連結</th>
                                    <th class="text-center" width="8%">排序</th>
                                    <th class="text-center" width="10%">權限限制</th>
                                    <th class="text-center" width="8%">狀態</th>
                                    <th class="text-center" width="12%">更新時間</th>
                                    <th class="text-center" width="12%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($list as $model): ?>
                                    <tr>
                                        <td class="text-center"><?= $model->id ?></td>
                                        <!-- <td class="text-center">
                                            <?php if (!empty($model->icon)): ?>
                                                <img src="<?= $model->icon ?>" width="60" height="60" alt="<?= $model->title ?>">
                                            <?php else: ?>
                                                無圖示
                                            <?php endif; ?>
                                        </td> -->
                                        <td class="text-center"><?= Html::encode($model->title) ?></td>
                                        <td class="text-center">
                                            <a href="<?= $model->url ?>" target="_blank">
                                                <?= Html::encode($model->url) ?>
                                            </a>
                                        </td>
                                        <td class="text-center"><?= $model->sort ?></td>
                                        <td class="text-center"><?= QuickLink::$isLoginLabel[$model->is_login] ?></td>
                                        <td class="text-center"><?= QuickLink::$statusLabel[$model->status] ?></td>
                                        <td class="text-center">
                                            <?= $model->update_time ?>
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
                            <?php echo Paging::widget(['start' => $start, 'count' => $count]) ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center">目前無資料</div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
