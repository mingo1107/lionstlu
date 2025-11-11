<?php

namespace ball\util;


class StringUtil
{
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function substring(string $data, int $start, int $length = null, string $encoding = null)
    {
        if (strlen($data)) {
            return mb_substr($data, $start, $length, $encoding);
        } else {
            return '';
        }
    }

}