<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $member common\models\MemberModel */
/* @var $resetLink string */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">
        台灣獅子大學 - 密碼重設
    </h2>

    <p>親愛的 <?= Html::encode($member->name ?: $member->username) ?>，您好：</p>

    <p>因為您在台灣獅子大學的網站點擊了重設密碼，所以收到這封信件。</p>

    <p>請點擊以下連結重設您的密碼：</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="<?= Html::encode($resetLink) ?>"
            style="display: inline-block; padding: 12px 30px; background-color: #4CAF50; 
                  color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
            重設密碼
        </a>
    </div>

    <p style="color: #666; font-size: 12px;">
        如果按鈕無法點擊，請複製以下網址到瀏覽器開啟：<br>
        <a href="<?= Html::encode($resetLink) ?>" style="color: #4CAF50; word-break: break-all;">
            <?= Html::encode($resetLink) ?>
        </a>
    </p>

    <p style="color: #999; font-size: 12px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
        此重設連結將在 7 天內有效。若這不是您操作的，請忽略此信件。<br>
        如有任何問題，歡迎聯繫客服。
    </p>

    <p style="color: #999; font-size: 11px; margin-top: 20px;">
        此為系統自動發送信件，請勿直接回覆。
    </p>
</div>