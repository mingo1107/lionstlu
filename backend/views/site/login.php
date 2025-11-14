<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \common\models\LoginForm */

use ball\helper\HtmlHelper;
use yii\helpers\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<h3>管理後台</h3>
<form class="m-t" role="form" method="post">
    <?=HtmlHelper::displayFlash()?>
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
           value="<?= Yii::$app->request->csrfToken ?>"/>
    <div class="form-group">
        <?= Html::activeTextInput($model, 'username', ['class' => 'form-control input-lg',
            'placeholder' => '帳號', 'required' => '']) ?>
    </div>
    <div class="form-group">
        <?= Html::activePasswordInput($model, 'password', ['class' => 'form-control input-lg',
            'placeholder' => '密碼', 'required' => '']) ?>
    </div>
    <button type="submit" class="btn btn-primary  block full-width m-b btn-lg">登入</button>
</form>
