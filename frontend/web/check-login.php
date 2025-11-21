<?php
/**
 * 登入狀態檢查頁面
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

$application = new yii\web\Application($config);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>登入狀態檢查</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        pre { background: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table td, table th { padding: 8px; border: 1px solid #ddd; text-align: left; }
        table th { background: #4CAF50; color: white; }
        .code { font-family: 'Courier New', monospace; background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 登入狀態詳細檢查</h1>
    
    <?php
    $isGuest = Yii::$app->user->isGuest;
    $userId = Yii::$app->user->id;
    $identity = Yii::$app->user->identity;
    
    // 檢查 Session
    $sessionId = Yii::$app->session->getId();
    $sessionIsActive = Yii::$app->session->getIsActive();
    
    // 檢查 AuthKey Cookie
    $authKeyCookieName = \ball\helper\Security::encrypt('_mks_');
    $authKeyCookie = isset($_COOKIE[$authKeyCookieName]) ? $_COOKIE[$authKeyCookieName] : null;
    $authKeyRaw = null;
    $authKeyDecrypted = null;
    
    if ($authKeyCookie) {
        try {
            $authKeyDecrypted = \ball\helper\Security::decrypt($authKeyCookie);
            $authKeyRaw = $authKeyDecrypted;
        } catch (Exception $e) {
            $authKeyRaw = "解密失敗: " . $e->getMessage();
        }
    }
    
    // 解析 AuthKey
    $authKeyParts = [];
    if ($authKeyDecrypted) {
        // 使用 | 分隔符（與 MemberModel::validateAuthKey 一致）
        $authKeyParts = explode('|', $authKeyDecrypted);
    }
    
    // 檢查 IP
    $currentIp = \ball\util\HttpUtil::ip();
    
    // 檢查 User Agent
    $currentUA = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
    ?>
    
    <div class="section">
        <h2>1. 基本登入狀態</h2>
        <table>
            <tr>
                <th>項目</th>
                <th>值</th>
                <th>狀態</th>
            </tr>
            <tr>
                <td>isGuest</td>
                <td><?= $isGuest ? 'true' : 'false' ?></td>
                <td class="<?= $isGuest ? 'error' : 'success' ?>">
                    <?= $isGuest ? '❌ 未登入' : '✅ 已登入' ?>
                </td>
            </tr>
            <tr>
                <td>User ID</td>
                <td><?= $userId ?? 'null' ?></td>
                <td class="<?= $userId ? 'success' : 'error' ?>">
                    <?= $userId ? '✅' : '❌' ?>
                </td>
            </tr>
            <tr>
                <td>Identity</td>
                <td><?= $identity ? get_class($identity) : 'null' ?></td>
                <td class="<?= $identity ? 'success' : 'error' ?>">
                    <?= $identity ? '✅' : '❌' ?>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <h2>2. Session 資訊</h2>
        <table>
            <tr>
                <th>項目</th>
                <th>值</th>
            </tr>
            <tr>
                <td>Session ID</td>
                <td><?= htmlspecialchars($sessionId) ?></td>
            </tr>
            <tr>
                <td>Session Active</td>
                <td class="<?= $sessionIsActive ? 'success' : 'error' ?>">
                    <?= $sessionIsActive ? '✅ Yes' : '❌ No' ?>
                </td>
            </tr>
            <tr>
                <td>$_SESSION['__id']</td>
                <td><?= isset($_SESSION['__id']) ? $_SESSION['__id'] : 'null' ?></td>
            </tr>
            <tr>
                <td>$_SESSION['__authKey']</td>
                <td><?= isset($_SESSION['__authKey']) ? (is_string($_SESSION['__authKey']) ? 'string' : gettype($_SESSION['__authKey'])) : 'null' ?></td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <h2>3. AuthKey Cookie 檢查</h2>
        <table>
            <tr>
                <th>項目</th>
                <th>值</th>
            </tr>
            <tr>
                <td>加密的 Cookie 名稱</td>
                <td class="code"><?= htmlspecialchars($authKeyCookieName) ?></td>
            </tr>
            <tr>
                <td>Cookie 是否存在</td>
                <td class="<?= $authKeyCookie ? 'success' : 'error' ?>">
                    <?= $authKeyCookie ? '✅ Yes' : '❌ No' ?>
                </td>
            </tr>
            <?php if ($authKeyCookie): ?>
            <tr>
                <td>加密的 Cookie 值（前50字）</td>
                <td class="code"><?= htmlspecialchars(substr($authKeyCookie, 0, 50)) ?>...</td>
            </tr>
            <tr>
                <td>解密後的 AuthKey</td>
                <td class="code"><?= htmlspecialchars($authKeyRaw) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    
    <?php if ($authKeyDecrypted && count($authKeyParts) == 4): ?>
    <div class="section">
        <h2>4. AuthKey 解析</h2>
        <table>
            <tr>
                <th>部分</th>
                <th>值</th>
                <th>說明</th>
            </tr>
            <tr>
                <td>Timestamp</td>
                <td class="code"><?= htmlspecialchars($authKeyParts[0]) ?></td>
                <td><?= date('Y-m-d H:i:s', intval($authKeyParts[0])) ?></td>
            </tr>
            <tr>
                <td>User ID</td>
                <td class="code"><?= htmlspecialchars($authKeyParts[1]) ?></td>
                <td>-</td>
            </tr>
            <tr>
                <td>IP</td>
                <td class="code"><?= htmlspecialchars($authKeyParts[2]) ?></td>
                <td>-</td>
            </tr>
            <tr>
                <td>User Agent（前50字）</td>
                <td class="code"><?= htmlspecialchars(substr($authKeyParts[3], 0, 50)) ?>...</td>
                <td>-</td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <h2>5. 驗證條件檢查</h2>
        <?php
        $checks = [
            'Timestamp 是數字' => is_numeric($authKeyParts[0]),
            'Timestamp > 0' => intval($authKeyParts[0]) > 0,
            'Timestamp <= 當前時間' => intval($authKeyParts[0]) <= time(),
            'User ID 匹配' => $identity ? ($authKeyParts[1] == $identity->getId()) : false,
            'User Agent 匹配' => $authKeyParts[3] == $currentUA,
            'IP 匹配' => $authKeyParts[2] == $currentIp,
        ];
        ?>
        <table>
            <tr>
                <th>檢查項目</th>
                <th>結果</th>
                <th>詳細</th>
            </tr>
            <tr>
                <td>Timestamp 是數字</td>
                <td class="<?= $checks['Timestamp 是數字'] ? 'success' : 'error' ?>">
                    <?= $checks['Timestamp 是數字'] ? '✅' : '❌' ?>
                </td>
                <td><?= $authKeyParts[0] ?></td>
            </tr>
            <tr>
                <td>Timestamp > 0</td>
                <td class="<?= $checks['Timestamp > 0'] ? 'success' : 'error' ?>">
                    <?= $checks['Timestamp > 0'] ? '✅' : '❌' ?>
                </td>
                <td><?= intval($authKeyParts[0]) ?></td>
            </tr>
            <tr>
                <td>Timestamp <= 當前時間</td>
                <td class="<?= $checks['Timestamp <= 當前時間'] ? 'success' : 'error' ?>">
                    <?= $checks['Timestamp <= 當前時間'] ? '✅' : '❌' ?>
                </td>
                <td>AuthKey: <?= intval($authKeyParts[0]) ?>, 當前: <?= time() ?></td>
            </tr>
            <tr>
                <td>User ID 匹配</td>
                <td class="<?= $checks['User ID 匹配'] ? 'success' : 'error' ?>">
                    <?= $checks['User ID 匹配'] ? '✅' : '❌' ?>
                </td>
                <td>AuthKey: <?= $authKeyParts[1] ?>, Identity: <?= $identity ? $identity->getId() : 'null' ?></td>
            </tr>
            <tr>
                <td>User Agent 匹配</td>
                <td class="<?= $checks['User Agent 匹配'] ? 'success' : 'error' ?>">
                    <?= $checks['User Agent 匹配'] ? '✅' : '❌' ?>
                </td>
                <td>
                    AuthKey: <?= htmlspecialchars(substr($authKeyParts[3], 0, 30)) ?>...<br>
                    當前: <?= htmlspecialchars(substr($currentUA, 0, 30)) ?>...
                </td>
            </tr>
            <tr>
                <td><strong>IP 匹配</strong></td>
                <td class="<?= $checks['IP 匹配'] ? 'success' : 'error' ?>">
                    <strong><?= $checks['IP 匹配'] ? '✅' : '❌' ?></strong>
                </td>
                <td>
                    <strong>AuthKey IP: <?= htmlspecialchars($authKeyParts[2]) ?></strong><br>
                    <strong>當前 IP: <?= htmlspecialchars($currentIp) ?></strong>
                </td>
            </tr>
        </table>
        
        <?php
        $allPass = array_reduce($checks, function($carry, $item) {
            return $carry && $item;
        }, true);
        ?>
        
        <div style="margin-top: 20px; padding: 15px; background: <?= $allPass ? '#e8f5e9' : '#ffebee' ?>; border-radius: 5px;">
            <?php if ($allPass): ?>
                <p class="success">✅ 所有驗證條件都通過！AuthKey 應該有效。</p>
            <?php else: ?>
                <p class="error">❌ 有驗證條件失敗！這就是 isGuest 為 true 的原因。</p>
            <?php endif; ?>
        </div>
    </div>
    <?php elseif ($authKeyDecrypted): ?>
    <div class="section">
        <h2>4. AuthKey 格式錯誤</h2>
        <p class="error">❌ AuthKey 無法正確分割為 4 個部分</p>
        <pre><?= htmlspecialchars($authKeyDecrypted) ?></pre>
        <p>分割結果（<?= count($authKeyParts) ?> 個部分）:</p>
        <pre><?php print_r($authKeyParts); ?></pre>
    </div>
    <?php endif; ?>
    
    <div class="section">
        <h2>6. 當前環境資訊</h2>
        <table>
            <tr>
                <th>項目</th>
                <th>值</th>
            </tr>
            <tr>
                <td>當前 IP</td>
                <td class="code"><?= htmlspecialchars($currentIp) ?></td>
            </tr>
            <tr>
                <td>User Agent</td>
                <td class="code"><?= htmlspecialchars($currentUA) ?></td>
            </tr>
            <tr>
                <td>當前時間</td>
                <td><?= date('Y-m-d H:i:s') ?> (<?= time() ?>)</td>
            </tr>
            <tr>
                <td>PHP 版本</td>
                <td><?= PHP_VERSION ?></td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <h2>7. 所有 Cookies</h2>
        <pre><?php print_r($_COOKIE); ?></pre>
    </div>
    
    <div class="section">
        <h2>8. 快速操作</h2>
        <p>
            <a href="/member/login" style="display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">前往登入</a>
            <a href="/" style="display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">前往首頁</a>
            <a href="javascript:location.reload()" style="display: inline-block; padding: 10px 20px; background: #FF9800; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">重新整理</a>
        </p>
    </div>
</body>
</html>

