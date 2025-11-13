<?php

namespace common\helpers;

use common\models\Media;
use common\models\MediaTrait;

/**
 * MediaHelper - 輔助函數，用於替代 MediaTrait::serialize() 的靜態調用
 * 解決 PHP 8.1+ 中 trait 靜態方法調用的廢棄警告
 */
class MediaHelper
{
    /**
     * 序列化媒體資料（替代 MediaTrait::serialize()）
     * 
     * @param object $model 使用 MediaTrait 的模型實例
     * @param string $attribute 屬性名稱
     * @param int $size 大小（預設為 1）
     * @return Media[]|Media
     */
    public static function serialize($model, string $attribute, int $size = 1)
    {
        // 直接使用 MediaTrait 的邏輯，但通過輔助類調用，避免廢棄警告
        $data = json_decode($model->$attribute);

        if ($size == 1) {
            if (!empty($data) && !empty($data[0])) {
                return $data[0];
            } else {
                return new Media();
            }
        } else {
            $mediaList = [];
            for ($i = 0; $i < $size; ++$i) {
                if (!empty($data[$i])) {
                    array_push($mediaList, $data[$i]);
                } else {
                    array_push($mediaList, new Media());
                }
            }
            return $mediaList;
        }
    }
}

