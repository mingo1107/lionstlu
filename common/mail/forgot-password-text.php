<?php
/* @var $this yii\web\View */
/* @var $member common\models\MemberModel */
/* @var $resetLink string */
?>
台灣獅子大學 - 密碼重設

親愛的 <?= $member->name ?: $member->username ?>，您好：

因為您在台灣獅子大學的網站點擊了重設密碼，所以收到這封信件。

請點擊以下連結重設您的密碼：

<?= $resetLink ?>

此重設連結將在 7 天內有效。若這不是您操作的，請忽略此信件。

如有任何問題，歡迎聯繫客服。

此為系統自動發送信件，請勿直接回覆。