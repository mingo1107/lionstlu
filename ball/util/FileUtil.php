<?php

namespace ball\util;


class FileUtil
{
    public static function randomTmpFile(string $ext = null)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path = "D:/";
        } else {
            $path = "/tmp";
        }
        $path = sprintf("%s/%s%s", $path, date('YmdHis'), rand(0, 100));
        if (empty($ext)) {
            return $path;
        } else {
            return $path . "." . $ext;
        }
    }

    public static function base64image(string $imageUrl, string $ext = 'jpg')
    {
        return "data:image/$ext;base64," . chunk_split(base64_encode(HttpUtil::curl($imageUrl, [], 'get')));
    }
}