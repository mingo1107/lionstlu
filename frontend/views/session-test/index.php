<?php

/* @var $this yii\web\View */
/* @var $sessionData array */
/* @var $userData array */
/* @var $cookies array */
/* @var $sessionConfig array */

$this->title = 'Session 測試頁面';
?>
<div class="session-test-index" style="padding: 20px; font-family: monospace;">
    <h1><?= $this->title ?></h1>
    
    <div class="alert alert-info">
        <strong>注意：</strong>此頁面僅供開發測試使用。生產環境應移除或加上適當的權限控制。
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2>快速測試</h2>
        <button onclick="setSession()" class="btn btn-primary">設定測試 Session</button>
        <button onclick="getSession()" class="btn btn-info">取得測試 Session</button>
        <button onclick="clearSession()" class="btn btn-danger">清除所有 Session</button>
        <button onclick="location.reload()" class="btn btn-default">重新載入頁面</button>
    </div>
    
    <div style="background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>Session 基本資訊</h2>
        <pre><?php var_dump($sessionData); ?></pre>
    </div>
    
    <div style="background: #e8f5e9; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>使用者資訊</h2>
        <pre><?php var_dump($userData); ?></pre>
    </div>
    
    <div style="background: #fff3e0; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>Cookie 資訊</h2>
        <pre><?php var_dump($cookies); ?></pre>
    </div>
    
    <div style="background: #f3e5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>Session 配置</h2>
        <pre><?php var_dump($sessionConfig); ?></pre>
    </div>
    
    <div style="background: #e0f2f1; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>$_SESSION (原始)</h2>
        <pre><?php var_dump($_SESSION); ?></pre>
    </div>
    
    <div style="background: #fce4ec; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>$_COOKIE (原始)</h2>
        <pre><?php var_dump($_COOKIE); ?></pre>
    </div>
    
    <div style="background: #e1f5fe; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>Yii::$app->user 詳細資訊</h2>
        <pre><?php 
        echo "isGuest: " . (Yii::$app->user->isGuest ? 'true' : 'false') . "\n";
        echo "id: " . Yii::$app->user->getId() . "\n";
        if (!Yii::$app->user->isGuest) {
            echo "identity class: " . get_class(Yii::$app->user->identity) . "\n";
            echo "identity attributes:\n";
            var_dump(Yii::$app->user->identity->attributes);
            
            // 檢查 Auth Key 驗證
            echo "\n=== Auth Key 驗證檢查 ===\n";
            $authKey = Yii::$app->user->identity->getAuthKey();
            echo "Auth Key: " . ($authKey ?: 'null') . "\n";
            if ($authKey) {
                $keyArray = explode("_", $authKey);
                echo "Auth Key 解析:\n";
                echo "  時間戳: " . (isset($keyArray[0]) ? $keyArray[0] : 'N/A') . "\n";
                echo "  使用者ID: " . (isset($keyArray[1]) ? $keyArray[1] : 'N/A') . "\n";
                echo "  IP: " . (isset($keyArray[2]) ? $keyArray[2] : 'N/A') . "\n";
                echo "  User Agent: " . (isset($keyArray[3]) ? substr($keyArray[3], 0, 50) . '...' : 'N/A') . "\n";
                echo "\n當前環境:\n";
                echo "  當前IP: " . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'N/A') . "\n";
                echo "  當前User Agent: " . (isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 50) . '...' : 'N/A') . "\n";
                echo "  驗證結果: " . (Yii::$app->user->identity->validateAuthKey($authKey) ? '通過' : '失敗') . "\n";
            }
        }
        ?></pre>
    </div>
    
    <div style="background: #fff9c4; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>Session 檔案位置（如果使用檔案儲存）</h2>
        <pre><?php 
        echo "session.save_path: " . ini_get('session.save_path') . "\n";
        echo "session.save_handler: " . ini_get('session.save_handler') . "\n";
        ?></pre>
    </div>
</div>

<script>
function setSession() {
    var value = 'test_value_' + new Date().getTime();
    fetch('/session-test/set?key=test_key&value=' + encodeURIComponent(value))
        .then(response => response.json())
        .then(data => {
            alert('設定成功：' + data.message);
            location.reload();
        });
}

function getSession() {
    fetch('/session-test/get?key=test_key')
        .then(response => response.json())
        .then(data => {
            alert('Session 值：' + (data.value || 'null') + '\n存在：' + data.exists);
        });
}

function clearSession() {
    if (confirm('確定要清除所有 Session 嗎？')) {
        fetch('/session-test/clear')
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
    }
}
</script>

