<?php
/* @var $this yii\web\View */
/* @var $member common\models\MemberModel */
/* @var $verifyLink string */
?>
台灣獅子大學 - 會員註冊驗證

親愛的 <?= $member->name ?: $member->username ?>，您好：

感謝您註冊成為台灣獅子大學的會員！

為了確保您的帳號安全，請點擊以下連結完成 Email 驗證：

<?= $verifyLink ?>

此驗證連結將在 7 天內有效。若這不是您註冊的帳號，請忽略此信件。

如有任何問題，歡迎聯繫客服。

此為系統自動發送信件，請勿直接回覆。

