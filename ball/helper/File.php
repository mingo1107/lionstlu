<?php

namespace ball\helper;

class File
{
    const ENDPOINT_BASE = YII_DEBUG ?
    'https://admin.lionstlu.org.tw/upload' : 'https://admin.lionstlu.org.tw/upload';
    const ENDPOINT_S3_BASE = YII_DEBUG ?
    'https://admin.lionstlu.org.tw/upload' : 'https://admin.lionstlu.org.tw/upload';
    // S3 dir
    const CATEGORY_PRODUCT = '/product';
    const CATEGORY_BANNER = '/banner';
    const CATEGORY_ARTICLE = '/article';
    const CATEGORY_CATEGORY = '/category';
    const CATEGORY_UPLOAD = '/drive';
    // upload file type
    const TYPE_FILE = 0;
    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;
    const TYPE_FOLDER = 10;
    // extension type
    public static $extVideo = ['mp4', 'webm', 'ogg'];
    public static $extImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
    // image version
    const IMG_VERSION_DEFAULT = '';
    const IMG_VERSION_DESKTOP = 'd';
    const IMG_VERSION_MOBILE = 'm';
    const IMG_VERSION_THUMBNAIL = 'thumbnail';

    /**
     * @param string $category
     * @param string $fileName
     * @param string $type
     * @return string
     */
    public static function img(string $category, string $fileName, string $type = self::IMG_VERSION_DEFAULT)
    {
        if (empty($fileName)) {
            return '';
        }
        if ($type == self::IMG_VERSION_DEFAULT) {
            return sprintf("%s%s/%s", self::ENDPOINT_BASE, $category, $fileName);
        } else {
            return sprintf("%s%s/%s_%s", self::ENDPOINT_BASE, $category, $fileName, $type);
        }
    }

    /**
     * @param string $category
     * @param string $fileName
     * @return string
     */
    public static function fs(string $category, string $fileName)
    {
        return sprintf("%s%s/%s", self::ENDPOINT_BASE, $category, $fileName);
    }

    public static function s3(string $category, string $fileName)
    {
        return sprintf("%s%s/%s",
            self::ENDPOINT_S3_BASE, $category, $fileName);
    }

    /**
     * @param string $fileName
     * @return int file type
     */
    public static function getType(string $fileName)
    {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($ext, File::$extImage)) {
            return static::TYPE_IMAGE;
        } else if (in_array($ext, File::$extVideo)) {
            return static::TYPE_VIDEO;
        } else {
            return static::TYPE_FILE;
        }
    }

    /**
     * get width and height of an image
     * @param string $imagePath
     * @return array|bool
     */
    public static function getLocalImageGeometry(string $imagePath)
    {
        $result = [];
        $imageFile = glob($imagePath);
        if ($imageFile === false) {
            return false;
        }
        $image = new \Imagick($imageFile);
        $d = $image->getImageGeometry();
        $result['width'] = $d['width'];
        $result['height'] = $d['height'];
        $image->destroy();
        return $result;
    }
}
