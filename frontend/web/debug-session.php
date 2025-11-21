<?php
/**
 * 簡單的 Session 除錯頁面
 * 不使用 Yii 框架，直接檢查 PHP Session
 */

// 啟動 session
session_start();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session 除錯</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .section { margin: 20px 0; padding: 15px; background: #f5f5f5; }
        pre { background: #fff; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <h1>PHP Session 原始狀態檢查</h1>
    
    <div class="section">
        <h2>1. PHP Session 基本資訊</h2>
        <table>
            <tr><th>項目</th><th>值</th></tr>
            <tr><td>Session ID</td><td><?= session_id() ?></td></tr>
            <tr><td>Session 名稱</td><td><?= session_name() ?></td></tr>
            <tr><td>Session 狀態</td><td><?= session_status() == PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE' ?></td></tr>
            <tr><td>Session 存儲路徑</td><td><?= session_save_path() ?></td></tr>
        </table>
    </div>
    
    <div class="section">
        <h2>2. $_SESSION 內容</h2>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
    
    <div class="section">
        <h2>3. $_COOKIE 內容</h2>
        <pre><?php print_r($_COOKIE); ?></pre>
    </div>
    
    <div class="section">
        <h2>4. Session 檔案內容</h2>
        <?php
        $sessionFile = session_save_path() . '/sess_' . session_id();
        if (file_exists($sessionFile)) {
            echo "<p>檔案存在：$sessionFile</p>";
            echo "<pre>" . htmlspecialchars(file_get_contents($sessionFile)) . "</pre>";
        } else {
            echo "<p style='color: red;'>檔案不存在：$sessionFile</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. 快速操作</h2>
        <p>
            <a href="/member/login">前往登入</a> |
            <a href="/">前往首頁</a> |
            <a href="?clear=1">清除 Session</a> |
            <a href="javascript:location.reload()">重新整理</a>
        </p>
        <?php
        if (isset($_GET['clear'])) {
            session_destroy();
            echo "<p style='color: green;'>✅ Session 已清除</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <p style="font-size: 12px; color: #666;">
            當前時間：<?= date('Y-m-d H:i:s') ?><br>
            PHP 版本：<?= PHP_VERSION ?>
        </p>
    </div>
</body>
</html>

