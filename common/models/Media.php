<?php

namespace common\models;


use ball\JSONColumn;

/**
 * @property string $src 網址
 * @property string $link 連結
 * @property string $alt SEO文字
 * @property string $width 寬度
 * @property string $height 長度
 */
class Media extends JSONColumn
{

    public function __construct()
    {
        foreach ($this->attributes() as $f) {
            $this->$f = '';
        }
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return ['src', 'link', 'alt', 'width', 'height'];
    }
}