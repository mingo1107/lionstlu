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
}

