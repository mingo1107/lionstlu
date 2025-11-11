<?php

use backend\widget\Breadcrumbs;
use backend\widget\Paging;
use ball\helper\HtmlHelper;
use ball\util\HttpUtil;
use common\models\ArticleModel;
use common\models\CustomerServiceModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $qs string */
/* @var $title string */
/* @var $actionLabel string */
/* @var $list \common\models\ArticleModel[] */
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
                    ArrayHelper::merge(["" => "狀態"], ArticleModel::$statusLabel),
                    ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::dropDownList('ad_type', yii::$app->request->get("ad_type"),
                    ArrayHelper::merge(["" => "廣告類型"], ArticleModel::$adLabel),
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
                                    <th class="text-center" width="5%">流水號</th>
                                    <th class="text-center" width="12%">廣告類型</th>
                                    <th class="text-center" width="15%">分類</th>
                                    <th class="text-center" width="15%">名稱</th>
                                    <th class="text-center" width="8%">picsee連結</th>
                                    <th class="text-center" width="8%">瀏覽數</th>
                                    <th class="text-center" width="8%">分享數</th>
                                    <th class="text-center" width="8%">狀態</th>
                                    <th class="text-center" width="15%">建立時間 / 更新時間</th>
                                    <th class="text-center" width="10%">操作</th>
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
                                            <input type="checkbox" id="_select-<?= $model->id ?>" name="_select[]"
                                                   value="<?= $model->id ?>"/>
                                        </th>
                                        <td class="text-center"><?= $model->id ?></td>
                                        <td class="text-center"><?= ArticleModel::$adLabel[$model->ad_type] ?></td>
                                        <td class="text-center"><?= $model->category_name ?></td>
                                        <td class="text-center"><?= $model->title ?></td>
                                        <td class="text-center">
                                            <?php if(!empty($model->picsee_link)){?>
                                            <?= $model->picsee_link ?>
                                            <?php }else{ ?>
                                            <a href=""></a>
                                            <a class="btn btn-general btn-block"
                                               href="/<?= Yii::$app->controller->id ?>/genpicsee<?= HttpUtil::buildQuery($_GET, [], ['id' => $model->id]) ?>">產生短連結</a>
                                            <?php }?>
                                        </td>
                                        <td class="text-center"><?= $model->views ?></td>
                                        <td class="text-center"><?= $model->share_count ?></td>
                                        <td class="text-center"><?= ArticleModel::$statusLabel[$model->status] ?></td>
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
