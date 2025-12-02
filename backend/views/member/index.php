<?php

use backend\widget\Breadcrumbs;
use backend\widget\Paging;
use ball\helper\HtmlHelper;
use ball\util\HttpUtil;
use common\models\AreaModel;
use common\models\MemberModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $list \common\models\MemberModel[] */
/* @var $start int */
/* @var $count int */
/* @var $areaList \common\models\AreaModel[] */
/* @var $search array */
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
        <form class="navbar-form navbar-left" role="search" method="get">
            <div class="form-group">
                <?= Html::dropDownList(
                    'area_id',
                    isset($search['area_id']) ? $search['area_id'] : '',
                    ArrayHelper::merge(['' => '全部區域'], ArrayHelper::map($areaList, 'id', 'area_name')),
                    ['class' => 'form-control']
                ) ?>
            </div>
            <div class="form-group">
                <?= Html::dropDownList(
                    'status',
                    isset($search['status']) ? $search['status'] : '',
                    ArrayHelper::merge(['' => '全部狀態'], MemberModel::$statusLabel),
                    ['class' => 'form-control']
                ) ?>
            </div>
            <div class="form-group">
                <?= Html::dropDownList(
                    'is_self_register',
                    isset($search['is_self_register']) ? $search['is_self_register'] : '',
                    ['' => '全部', '1' => '自行註冊', '0' => '後台建立'],
                    ['class' => 'form-control']
                ) ?>
            </div>
            <div class="form-group">
                <?= Html::textInput(
                    'keyword',
                    isset($search['keyword']) ? $search['keyword'] : '',
                    ['class' => 'form-control', 'placeholder' => '搜尋關鍵字（姓名/帳號/Email/手機）']
                ) ?>
            </div>
            <button type="submit" class="btn btn-default">搜尋</button>
            <a href="/<?= Yii::$app->controller->id ?>/index" class="btn btn-default">清除</a>
        </form>
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= $title ?></h5>
                </div>
                <div class="ibox-content">
                    <?= HtmlHelper::displayFlash() ?>
                    <div>
                        <a href="/<?= Yii::$app->controller->id ?>/create"
                            class="btn btn-primary">建立<?= $actionLabel ?></a>
                        <a href="/<?= Yii::$app->controller->id ?>/import"
                            class="btn btn-success">匯入<?= $actionLabel ?></a>
                        <a href="/<?= Yii::$app->controller->id ?>/download-template"
                            class="btn btn-success">匯出會員Excel</a>
                    </div>
                    <?php if (!empty($list)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="3%">
                                            <input type="checkbox" id="_select-all" name="_select-all" value="1" />
                                        </th>
                                        <th class="text-center" width="5%">ID</th>
                                        <th class="text-center" width="11%">帳號/姓名</th>
                                        <th class="text-center" width="9%">區域/分會</th>
                                        <th class="text-center" width="8%">電話</th>
                                        <th class="text-center" width="5%">狀態</th>
                                        <th class="text-center" width="5%">驗證</th>
                                        <th class="text-center" width="14%">會員期限</th>
                                        <th class="text-center" width="5%">登入次數</th>
                                        <th class="text-center" width="9%">最後登入</th>
                                        <th class="text-center" width="9%">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($list as $model): ?>
                                        <?php
                                        // 計算會員期限狀態
                                        $periodText = '未設定';
                                        $periodClass = 'text-muted';

                                        if (!empty($model->period_start) || !empty($model->period_end)) {
                                            $periodStart = $model->period_start ?: '不限';
                                            $periodEnd = $model->period_end ?: '不限';
                                            $periodText = $periodStart . '<br>～<br>' . $periodEnd;

                                            // 檢查是否過期或未生效
                                            $now = time();
                                            if (!empty($model->period_end) && strtotime($model->period_end . ' 23:59:59') < $now) {
                                                $periodClass = 'text-danger'; // 已過期
                                            } elseif (!empty($model->period_start) && strtotime($model->period_start) > $now) {
                                                $periodClass = 'text-warning'; // 未生效
                                            } else {
                                                $periodClass = 'text-success'; // 有效期內
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <th class="text-center">
                                                <input type="checkbox" id="_select-<?= $model->id ?>" name="_select[]"
                                                    value="<?= $model->id ?>" />
                                            </th>
                                            <td class="text-center"><?= str_pad($model->id, 4, '0', STR_PAD_LEFT) ?></td>
                                            <td class="text-center">
                                                <div><?= Html::encode($model->username) ?></div>
                                                <div style="font-size: 12px; color: #999;"><?= Html::encode($model->name) ?></div>
                                            </td>
                                            <td class="text-center">
                                                <div><?= !empty($model->area_name) ? Html::encode($model->area_name) : '未設定' ?></div>
                                                <?php if (!empty($model->club_name)): ?>
                                                    <div style="font-size: 12px; color: #999;"><?= Html::encode($model->club_name) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center" style="font-size: 12px;"><?= !empty($model->mobile) ? Html::encode($model->mobile) : '-' ?></td>
                                            <td class="text-center"><?= MemberModel::$statusLabel[$model->status] ?></td>
                                            <td class="text-center"><?= isset(MemberModel::$validateLabel[$model->validate]) ? MemberModel::$validateLabel[$model->validate] : '未知' ?></td>
                                            <td class="text-center <?= $periodClass ?>" style="font-size: 12px;">
                                                <?= $periodText ?>
                                            </td>
                                            <td class="text-center"><?= $model->login_count ?></td>
                                            <td class="text-center" style="font-size: 12px;"><?= $model->last_login_time ?: '無' ?></td>
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
