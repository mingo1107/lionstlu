<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $member common\models\MemberModel */
/* @var $verifyLink string */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">
        台灣獅子大學 - 會員註冊驗證
    </h2>
    
    <p>親愛的 <?= Html::encode($member->name ?: $member->username) ?>，您好：</p>
    
    <p>感謝您註冊成為台灣獅子大學的會員！</p>
    
    <p>為了確保您的帳號安全，請點擊以下連結完成 Email 驗證：</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="<?= Html::encode($verifyLink) ?>" 
           style="display: inline-block; padding: 12px 30px; background-color: #4CAF50; 
                  color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
            驗證 Email
        </a>
    </div>
    
    <p style="color: #666; font-size: 12px;">
        如果按鈕無法點擊，請複製以下網址到瀏覽器開啟：<br>
        <a href="<?= Html::encode($verifyLink) ?>" style="color: #4CAF50; word-break: break-all;">
            <?= Html::encode($verifyLink) ?>
        </a>
    </p>
    
    <p style="color: #999; font-size: 12px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
        此驗證連結將在 7 天內有效。若這不是您註冊的帳號，請忽略此信件。<br>
        如有任何問題，歡迎聯繫客服。
    </p>
    
    <p style="color: #999; font-size: 11px; margin-top: 20px;">
        此為系統自動發送信件，請勿直接回覆。
    </p>
</div>

