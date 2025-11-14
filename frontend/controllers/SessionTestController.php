<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\MemberModel;
use ball\util\HttpUtil;

/**
 * Session 測試控制器
 * 用於除錯和檢查 session 狀態
 * 
 * 注意：此控制器僅供開發測試使用，生產環境應移除或加上適當的權限控制
 */
class SessionTestController extends Controller
{
    /**
     * 允許所有訪客訪問（僅開發環境）
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        // 僅在開發環境允許訪問
                        'matchCallback' => function ($rule, $action) {
                            return YII_ENV_DEV || YII_DEBUG;
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Session 測試頁面
     * 訪問：/session-test/index
     */
    public function actionIndex()
    {
        // 確保 session 已啟動
        Yii::$app->session->open();

        // 取得所有 session 資料
        $allSessionData = [];
        if (Yii::$app->session->getIsActive()) {
            // 透過 $_SESSION 取得所有資料
            $allSessionData = $_SESSION;
        }

        $sessionData = [
            'session_id' => Yii::$app->session->getId(),
            'session_name' => Yii::$app->session->getName(),
            'is_active' => Yii::$app->session->getIsActive(),
            'has_session_id' => !empty(Yii::$app->session->getId()),
            'all_session_data' => $allSessionData,
        ];

        // 檢查 session 中的用戶 ID（Yii2 內部使用的 key）
        // Yii2 使用 '__id' 作為 session key 來存儲用戶 ID
        $sessionUserId = null;
        if (isset($_SESSION['__id'])) {
            $sessionUserId = $_SESSION['__id'];
        }

        // 也檢查其他可能的 session keys
        $sessionKeys = [];
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, '__') === 0 || strpos($key, 'user') !== false) {
                $sessionKeys[$key] = $value;
            }
        }

        // 測試 validateAuthKey
        $authKeyValidation = null;
        if (isset($sessionKeys['__authKey']) && $sessionUserId) {
            $testIdentity = MemberModel::findIdentity($sessionUserId);
            if ($testIdentity) {
                $authKey = $sessionKeys['__authKey'];
                // 先嘗試使用 | 分隔符（新格式）
                $keyArray = explode("|", $authKey);
                // 如果失敗，嘗試使用 _ 分隔符（舊格式）
                if (count($keyArray) != 4) {
                    $parts = [];
                    $remaining = $authKey;
                    for ($i = 0; $i < 3; $i++) {
                        $pos = strpos($remaining, '_');
                        if ($pos === false) {
                            break;
                        }
                        $parts[] = substr($remaining, 0, $pos);
                        $remaining = substr($remaining, $pos + 1);
                    }
                    $parts[] = $remaining;
                    $keyArray = $parts;
                }
                $authKeyValidation = [
                    'authKey' => $authKey,
                    'validateResult' => $testIdentity->validateAuthKey($authKey),
                    'currentIP' => HttpUtil::ip(),
                    'currentUserAgent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'N/A',
                    'keyArray' => $keyArray,
                    'timestamp_check' => [
                        'timestamp' => isset($keyArray[0]) ? intval($keyArray[0]) : null,
                        'current_time' => time(),
                        'is_valid' => isset($keyArray[0]) && is_numeric($keyArray[0]) && intval($keyArray[0]) > 0 && intval($keyArray[0]) <= time(),
                    ],
                    'user_id_check' => [
                        'key_user_id' => isset($keyArray[1]) ? $keyArray[1] : null,
                        'actual_user_id' => $testIdentity->getId(),
                        'is_match' => isset($keyArray[1]) && $keyArray[1] == $testIdentity->getId(),
                    ],
                    'ip_check' => [
                        'key_ip' => isset($keyArray[2]) ? $keyArray[2] : null,
                        'current_ip' => HttpUtil::ip(),
                        'is_match' => isset($keyArray[2]) && $keyArray[2] == HttpUtil::ip(),
                    ],
                    'user_agent_check' => [
                        'key_ua' => isset($keyArray[3]) ? substr($keyArray[3], 0, 50) . '...' : null,
                        'current_ua' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 50) . '...' : 'N/A',
                        'is_match' => isset($keyArray[3]) && isset($_SERVER['HTTP_USER_AGENT']) && $keyArray[3] == $_SERVER['HTTP_USER_AGENT'],
                    ],
                ];
            }
        }

        // 使用者資訊
        $userData = [
            'is_guest' => Yii::$app->user->isGuest,
            'user_id' => Yii::$app->user->getId(),
            'session_user_id' => $sessionUserId, // Session 中保存的用戶 ID
            'identity' => Yii::$app->user->identity ? [
                'id' => Yii::$app->user->identity->id,
                'username' => Yii::$app->user->identity->username,
                'email' => Yii::$app->user->identity->email,
                'name' => Yii::$app->user->identity->name,
                'status' => Yii::$app->user->identity->status,
            ] : null,
            'findIdentity_test' => $sessionUserId ? MemberModel::findIdentity($sessionUserId) : null, // 測試 findIdentity
            'session_keys' => $sessionKeys, // Session 中與用戶相關的 keys
            'authKey_validation' => $authKeyValidation, // AuthKey 驗證結果
        ];

        // Cookie 資訊
        $cookies = [];
        foreach ($_COOKIE as $name => $value) {
            $cookies[$name] = [
                'value' => $value,
                'length' => strlen($value),
            ];
        }

        // Session 配置
        $sessionConfig = [
            'cookieParams' => Yii::$app->session->getCookieParams(),
            'timeout' => ini_get('session.gc_maxlifetime'),
        ];

        return $this->render('index', [
            'sessionData' => $sessionData,
            'userData' => $userData,
            'cookies' => $cookies,
            'sessionConfig' => $sessionConfig,
        ]);
    }

    /**
     * 設定測試 Session 值
     */
    public function actionSet()
    {
        $key = Yii::$app->request->get('key', 'test_key');
        $value = Yii::$app->request->get('value', 'test_value_' . time());

        Yii::$app->session->set($key, $value);

        return $this->asJson([
            'success' => true,
            'message' => "已設定 session[$key] = $value",
            'session_id' => Yii::$app->session->getId(),
        ]);
    }

    /**
     * 取得 Session 值
     */
    public function actionGet()
    {
        $key = Yii::$app->request->get('key', 'test_key');
        $value = Yii::$app->session->get($key);

        return $this->asJson([
            'key' => $key,
            'value' => $value,
            'exists' => Yii::$app->session->has($key),
            'session_id' => Yii::$app->session->getId(),
        ]);
    }

    /**
     * 清除所有 Session
     */
    public function actionClear()
    {
        Yii::$app->session->destroy();

        return $this->asJson([
            'success' => true,
            'message' => 'Session 已清除',
        ]);
    }
}
