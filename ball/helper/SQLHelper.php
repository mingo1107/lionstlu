<?php

namespace ball\helper;


use Yii;

class SQLHelper
{
    public static function buildSearchQuery(array $fields, string $method = 'get')
    {
        $search = [];
        if ($method == 'get') {
            foreach ($fields as $f) {
                $search[$f] = trim(Yii::$app->request->get($f));
            }
        } else if ($method == 'post') {
            foreach ($fields as $f) {
                $search[$f] = trim(Yii::$app->request->post($f));
            }
        }
        return $search;
    }
}