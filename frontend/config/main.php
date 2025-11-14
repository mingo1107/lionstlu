<?php

use common\models\MemberModel;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'ball',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'user'], // 確保 user 組件在 bootstrap 階段初始化
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\MemberModel',
            'enableAutoLogin' => true,
            'enableSession' => true, // 明確啟用 session（預設為 true，但明確設定更安全）
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'on afterLogin' => function ($event) {
                /**
                 * @var $identity MemberModel
                 */
                $identity = $event->identity;
                $identity->applyLoginInfo();
            },
            'on afterLogout' => function ($event) {
                /**
                 * @var $identity MemberModel
                 */
                $identity = $event->identity;
                $identity->logout();
            }
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
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
