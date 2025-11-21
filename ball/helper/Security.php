<?php

namespace ball\helper;


use Yii;

class Security
{

    public static function encrypt(string $data, string $method = 'AES-256-CBC')
    {
        $encrypted = openssl_encrypt(
            $data,
            $method,
            Yii::$app->request->cookieValidationKey,
            1,
            substr(Yii::$app->request->cookieValidationKey, 0, 16)
        );
        
        // 使用 URL-safe base64 編碼：將 + 替換為 -, / 替換為 _, 移除 =
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($encrypted));
    }

    public static function decrypt(string $data, string $method = 'AES-256-CBC')
    {
        // 還原 URL-safe base64：將 - 替換回 +, _ 替換回 /
        $data = str_replace(['-', '_'], ['+', '/'], $data);
        
        // 補回可能缺少的 = 填充
        $padding = strlen($data) % 4;
        if ($padding > 0) {
            $data .= str_repeat('=', 4 - $padding);
        }
        
        return openssl_decrypt(
            base64_decode($data),
            $method,
            Yii::$app->request->cookieValidationKey,
            1,
            substr(Yii::$app->request->cookieValidationKey, 0, 16)
        );
    }
}
