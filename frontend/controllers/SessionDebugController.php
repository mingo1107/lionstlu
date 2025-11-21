<?php

namespace frontend\controllers;

use ball\helper\Security;
use ball\util\HttpUtil;
use Yii;
use yii\web\Controller;

/**
 * Session Debug Controller
 * 用於測試 session 和 authKey 驗證
 */
class SessionDebugController extends Controller
{
    /**
     * 顯示 session 調試資訊
     */
    public function actionIndex()
    {
        $debugInfo = [
            'timestamp' => date('Y-m-d H:H:i:s'),
            'user' => [
                'isGuest' => Yii::$app->user->isGuest,
                'id' => Yii::$app->user->id,
                'identity' => Yii::$app->user->identity ? get_class(Yii::$app->user->identity) : null,
                'identityData' => Yii::$app->user->identity ? [
                    'id' => Yii::$app->user->identity->id,
                    'name' => Yii::$app->user->identity->name,
                    'email' => Yii::$app->user->identity->email,
                ] : null,
            ],
            'session' => [
                'id' => Yii::$app->session->id,
                'isActive' => Yii::$app->session->isActive,
                '__id' => Yii::$app->session->get('__id'),
                '__authKey' => Yii::$app->session->get('__authKey'),
            ],
            'cookies' => [
                'session' => isset($_COOKIE[Yii::$app->session->name]) ? 'SET' : 'NOT SET',
                'csrf' => isset($_COOKIE['_csrf-frontend']) ? 'SET' : 'NOT SET',
            ],
            'authKey' => [
                'cookieExists' => HttpUtil::getCookie('_mks_') !== false,
                'cookieValue' => HttpUtil::getCookie('_mks_'),
            ],
            'config' => [
                'cookieValidationKey' => isset(Yii::$app->request->cookieValidationKey) ? 'SET' : 'NOT SET',
                'sessionName' => Yii::$app->session->name,
                'identityClass' => Yii::$app->user->identityClass,
                'enableSession' => Yii::$app->user->enableSession,
                'enableAutoLogin' => Yii::$app->user->enableAutoLogin,
            ],
        ];

        // 如果用戶已登入，測試 authKey 驗證
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity) {
            $authKey = HttpUtil::getCookie('_mks_');
            if ($authKey !== false) {
                $debugInfo['authKey']['validation'] = [
                    'result' => Yii::$app->user->identity->validateAuthKey($authKey),
                    'parts' => explode('|', $authKey),
                ];
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($debugInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * 測試 Cookie 加密/解密
     */
    public function actionTestCookie()
    {
        $testData = 'test_' . time();
        $encrypted = Security::encrypt($testData);
        $decrypted = Security::decrypt($encrypted);

        $result = [
            'original' => $testData,
            'encrypted' => $encrypted,
            'decrypted' => $decrypted,
            'match' => $testData === $decrypted,
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * 清除 session 和 cookie
     */
    public function actionClear()
    {
        // 登出用戶
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        // 銷毀 session
        Yii::$app->session->destroy();

        // 清除所有 cookie
        HttpUtil::deleteAllCookies();

        echo json_encode([
            'status' => 'success',
            'message' => 'Session 和 Cookie 已清除',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Cookie 加密診斷
     */
    public function actionCookieEncryption()
    {
        $testKey = '_mks_';
        $encryptedKey = Security::encrypt($testKey);
        
        $result = [
            'test' => [
                'original_key' => $testKey,
                'encrypted_key' => $encryptedKey,
                'encrypted_key_length' => strlen($encryptedKey),
            ],
            'config' => [
                'cookieValidationKey' => substr(Yii::$app->request->cookieValidationKey, 0, 10) . '...',
                'cookieValidationKey_length' => strlen(Yii::$app->request->cookieValidationKey),
            ],
            'cookies' => [
                'all_cookie_keys' => array_keys($_COOKIE),
                'target_cookie_exists' => isset($_COOKIE[$encryptedKey]),
                'target_cookie_value' => isset($_COOKIE[$encryptedKey]) ? substr($_COOKIE[$encryptedKey], 0, 50) . '...' : 'NOT FOUND',
            ],
            'test_decrypt' => [],
        ];

        // 測試解密
        if (isset($_COOKIE[$encryptedKey])) {
            try {
                $decryptedValue = Security::decrypt($_COOKIE[$encryptedKey]);
                $result['test_decrypt'] = [
                    'success' => true,
                    'value_preview' => substr($decryptedValue, 0, 100) . '...',
                    'value_length' => strlen($decryptedValue),
                ];
            } catch (\Exception $e) {
                $result['test_decrypt'] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        } else {
            $result['test_decrypt'] = [
                'success' => false,
                'error' => 'Cookie not found',
            ];
        }

        // 使用 HttpUtil::getCookie 測試
        $result['httputil_test'] = [
            'getCookie_result' => HttpUtil::getCookie($testKey),
            'getCookie_result_type' => gettype(HttpUtil::getCookie($testKey)),
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * 詳細的 authKey 驗證診斷
     */
    public function actionValidateAuthKey()
    {
        $sessionId = Yii::$app->session->get('__id');
        
        if (!$sessionId) {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No user ID in session',
            ]);
        }

        // 從資料庫加載用戶
        $member = \common\models\MemberModel::findIdentity($sessionId);
        
        if (!$member) {
            return $this->asJson([
                'status' => 'error',
                'message' => 'User not found',
            ]);
        }

        // 獲取 authKey
        $authKey = HttpUtil::getCookie('_mks_');
        
        if (!$authKey) {
            return $this->asJson([
                'status' => 'error',
                'message' => 'AuthKey cookie not found',
            ]);
        }

        // 解析 authKey
        $keyArray = explode("|", $authKey);
        
        // 詳細驗證
        $result = [
            'authKey' => $authKey,
            'parsed' => [
                'timestamp' => $keyArray[0] ?? null,
                'user_id' => $keyArray[1] ?? null,
                'ip' => $keyArray[2] ?? null,
                'user_agent' => isset($keyArray[3]) ? substr($keyArray[3], 0, 100) . '...' : null,
            ],
            'current' => [
                'timestamp' => time(),
                'user_id' => $member->getId(),
                'ip' => HttpUtil::ip(),
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 100) . '...',
            ],
            'validation' => [
                'timestamp_valid' => is_numeric($keyArray[0] ?? '') && intval($keyArray[0]) > 0 && intval($keyArray[0]) <= time(),
                'user_id_match' => isset($keyArray[1]) && $keyArray[1] == $member->getId(),
                'ip_match' => isset($keyArray[2]) && $keyArray[2] == HttpUtil::ip(),
                'user_agent_match' => isset($keyArray[3]) && $keyArray[3] == $_SERVER['HTTP_USER_AGENT'],
            ],
            'overall_result' => $member->validateAuthKey($authKey),
        ];

        return $this->asJson($result);
    }

    /**
     * 強制重新生成 authKey
     */
    public function actionRegenerateAuthKey()
    {
        $sessionId = Yii::$app->session->get('__id');
        
        if (!$sessionId) {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No user ID in session',
            ]);
        }

        // 從資料庫加載用戶
        $member = \common\models\MemberModel::findIdentity($sessionId);
        
        if (!$member) {
            return $this->asJson([
                'status' => 'error',
                'message' => 'User not found',
            ]);
        }

        // 重新生成 authKey
        $member->generateAuthKey();

        // 更新 session
        Yii::$app->session->set('__authKey', HttpUtil::getCookie('_mks_'));

        return $this->asJson([
            'status' => 'success',
            'message' => 'AuthKey regenerated',
            'new_authKey' => substr(HttpUtil::getCookie('_mks_'), 0, 50) . '...',
            'encrypted_cookie_name' => Security::encrypt('_mks_'),
        ]);
    }
}

