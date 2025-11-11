<?php

use backend\widget\Breadcrumbs;
use backend\widget\Paging;
use ball\helper\HtmlHelper;
use ball\order\OrderStatus;
use ball\util\HttpUtil;
use ball\util\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $list \common\models\OrdersModel[] */
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
                <?= Html::dropDownList('status', yii::$app->request->get("status"),
                    ArrayHelper::merge(["" => "狀態"], OrderStatus::$labels),
                    ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::textInput('keyword', yii::$app->request->get("keyword"),
                    ['class' => 'form-control', 'placeholder' => '搜尋關鍵字']) ?>
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
                                    <th class="text-center" width="30%">編號 / 購買人</th>
                                    <th class="text-center" width="8%">訂單狀態</th>
                                    <th class="text-center" width="15%">建立時間 / 更新時間</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($list as $model): ?>
                                    <tr>
                                        <th class="text-center" width="5%">
                                            <input type="checkbox" id="_select-<?= $model->id ?>" name="_select[]"
                                                   value="<?= $model->id ?>"/>
                                        </th>
                                        <td class="text-center">
                                            <div>
                                                <a href="<?= Url::to(["update", 'id' => $model->id]) ?>"><?= $model->no ?></a>
                                            </div>
                                            <div><?= $model->name ?></div>
                                        </td>
                                        <td class="text-center"><?= OrderStatus::$labels[$model->status] ?></td>
                                        <td class="text-center">
                                            <div><?= $model->create_time ?></div>
                                            <div><?= $model->update_time ?></div>
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
