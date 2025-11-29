<?php

use backend\widget\Breadcrumbs;
use ball\helper\HtmlHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $insertCount int */
/* @var $updateCount int */
/* @var $failCount int */
/* @var $failedRecords array */

$title = '匯入會員';
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

                    <div class="row">


                        <div class="col-lg-12 text-right">
                            <a href="/<?= Yii::$app->controller->id ?>/download-template" class="btn btn-success">匯出會員Excel</a>
                            <a href="/<?= Yii::$app->controller->id ?>/download-empty-template" class="btn btn-info">匯出空白Excel</a>
                        </div>


                        <div class="col-lg-12">
                            <div class="alert alert-info">
                                <h4>匯入說明</h4>
                                <ul>
                                    <li>請使用 Excel 檔案（.xlsx 或 .xls）</li>
                                    <li>第一行為標題行，資料從第二行開始</li>
                                    <li>欄位順序：ID(會員編號)、區、帳號(Email)、密碼、名稱、手機、生日、所在城市、所在區域、所在地址、其他城市、會員期限起、會員期限訖</li>
                                    <li>Email 為必填欄位，將作為會員帳號</li>
                                    <li>如果 Email 已存在，將更新該會員資料</li>
                                    <li>城市名稱：「臺」會自動轉換為「台」</li>
                                    <li>區域名稱：如果沒有「區」字，系統會自動補上</li>
                                    <li><strong class="text-danger">重要：「區」欄位的區域名稱必須已存在於系統中，否則該筆資料將匯入失敗</strong></li>
                                    <li>建議先到「區域管理」確認或建立所需的區域</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                            value="<?= Yii::$app->request->csrfToken ?>" />

                        <div class="form-group">
                            <label class="col-sm-2 control-label">選擇 Excel 檔案</label>
                            <div class="col-sm-10">
                                <input type="file" name="import_file" accept=".xlsx,.xls" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">開始匯入</button>
                                <a href="/<?= Yii::$app->controller->id ?>/index" class="btn btn-default">返回列表</a>
                            </div>
                        </div>
                    </form>

                    <?php if ($insertCount > 0 || $updateCount > 0 || $failCount > 0): ?>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h3>匯入結果</h3>
                                <div class="row">
                                    <?php if ($insertCount > 0): ?>
                                        <div class="col-md-4">
                                            <div class="alert alert-success">
                                                <strong>新增：</strong><?= $insertCount ?> 筆
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($updateCount > 0): ?>
                                        <div class="col-md-4">
                                            <div class="alert alert-info">
                                                <strong>更新：</strong><?= $updateCount ?> 筆
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($failCount > 0): ?>
                                        <div class="col-md-4">
                                            <div class="alert alert-danger">
                                                <strong>失敗：</strong><?= $failCount ?> 筆
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($failedRecords)): ?>
                                    <h4>失敗記錄明細</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="5%">行號</th>
                                                    <th width="10%">會員編號</th>
                                                    <th width="8%">區域</th>
                                                    <th width="15%">Email</th>
                                                    <th width="10%">姓名</th>
                                                    <th width="12%">手機</th>
                                                    <th width="10%">城市</th>
                                                    <th width="30%">錯誤原因</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($failedRecords as $record): ?>
                                                    <tr>
                                                        <td><?= $record['row'] ?></td>
                                                        <td><?= Html::encode($record['data'][0] ?? '') ?></td>
                                                        <td><?= Html::encode($record['data'][1] ?? '') ?></td>
                                                        <td><?= Html::encode($record['data'][2] ?? '') ?></td>
                                                        <td><?= Html::encode($record['data'][4] ?? '') ?></td>
                                                        <td><?= Html::encode($record['data'][5] ?? '') ?></td>
                                                        <td><?= Html::encode($record['data'][7] ?? '') ?></td>
                                                        <td class="text-danger"><?= Html::encode($record['error']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
