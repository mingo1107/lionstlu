<?php

namespace ball\helper;


use Yii;

class Security
{

    public static function encrypt(string $data, string $method = 'AES-256-CBC')
    {
        return str_replace("=", "", base64_encode(openssl_encrypt(
            $data,
            $method,
            Yii::$app->request->cookieValidationKey,
            1,
            substr(Yii::$app->request->cookieValidationKey, 0, 16)
        )));
    }

    public static function decrypt(string $data, string $method = 'AES-256-CBC')
    {
        return str_replace("=", "", openssl_decrypt(
            base64_decode($data),
            $method,
            Yii::$app->request->cookieValidationKey,
            1,
            substr(Yii::$app->request->cookieValidationKey, 0, 16)
        ));
    }
}
