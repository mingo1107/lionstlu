<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'ball-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log', 'user'], // 確保 user 組件在 bootstrap 階段初始化
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\UserModel',
            'enableAutoLogin' => true,
            'enableSession' => true, // 明確啟用 session（預設為 true，但明確設定更安全）
            'identityCookie' => [
                'name' => '_identity-ball-backend',
                'httpOnly' => true,
                'secure' => !YII_DEBUG, // 正式環境自動啟用 HTTPS secure flag
                'sameSite' => 'Lax',
            ],
            'on afterLogin' => function ($event) {
                /**
                 * @var $identity \common\models\UserModel
                 */
                $identity = $event->identity;
                $identity->applyLoginInfo();
            },
            'on afterLogout' => function ($event) {
                /**
                 * @var $identity \common\models\UserModel
                 */
                $identity = $event->identity;
                $identity->logout();
            }
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
            'timeout' => 3600 * 24, // Session 過期時間：24 小時
            'useCookies' => true,
            'cookieParams' => [
                'httpOnly' => true,
                'secure' => !YII_DEBUG, // 正式環境自動啟用 HTTPS secure flag
                'sameSite' => 'Lax',
                'lifetime' => 0, // 瀏覽器關閉時過期
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info', 'trace'],
                    'categories' => ['member-import', 'application'],
                    'logFile' => '@runtime/logs/app.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];
