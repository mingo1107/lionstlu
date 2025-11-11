<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=mysql;dbname=ball_lionstlu',
            'username' => '',
            'password' => '',
            'charset' => 'utf8',
        ],
//        'db' => [
        //            'class' => 'yii\db\Connection',
        //            'dsn' => 'mysql:host=localhost;dbname=ball',
        //            'username' => 'root',
        //            'password' => '3065'
        //        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'encryption' => 'tls',
                'host' => 'smtp.gmail.com',
                'port' => '587',
                'streamOptions' => [
                    'ssl' => ['allow_self_signed' => true, 'verify_peer' => false],
                ],
                'username' => 'windtalk168@gmail.com',
                'password' => 'ji3g4balladmin',
            ],
        ],
    ],
    'timeZone' => 'Asia/Taipei',
    'language' => 'zh-TW',
];
