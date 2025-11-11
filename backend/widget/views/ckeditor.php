<?php

use backend\assets\CKEditorAsset;
use backend\widget\InlineScript;
use yii\helpers\Html;

/* @var $model \yii\db\ActiveRecord */
/* @var $attribute string */
/* @var $options array */
CKEditorAsset::register($this);
?>
<?= Html::activeTextarea($model, $attribute, $options) ?>
<?php InlineScript::begin() ?>
<script>
    CKEDITOR.replace('<?= Html::getInputName($model, $attribute) ?>');
</script>
<?php InlineScript::end() ?>
