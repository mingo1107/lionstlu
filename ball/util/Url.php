<?php

namespace ball\util;


use Yii;

class Url extends \yii\helpers\Url
{
    /**
     * Append query string passed by previous page to url if $url is array
     * @param string $url
     * @param bool $scheme
     * @return string
     */
    public static function to($url = '', $scheme = false)
    {
        if (is_array($url) && !empty($_GET)) {
            $params = $url;
            unset($params[0]);

            // set value from query string
            foreach ($_GET as $k => $v) {
                if (!isset($params[$k])) {
                    $url[$k] = $v;
                }
            }

            // check and set field to null
            foreach ($params as $k => $v) {
                if ($params[$k] === null) {
                    $url[$k] = null;
                }
            }
        }
        return parent::to($url, $scheme);
    }

    public static function order(string $field)
    {
        if (yii::$app->request->get("order") != "desc") {
            return static::to(["", "orderby" => $field, "order" => "desc"]);
        } else {
            return static::to(["", "orderby" => $field, "order" => ""]);
        }
    }
}