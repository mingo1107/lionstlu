<?php


namespace ball\facebook;


use ball\util\HttpUtil;
use yii\base\Component;

class FacebookGraph extends Component
{
    const ENDPOINT_BASE = "https://graph.facebook.com";

    public function get(string $endpoint, array $data = [], $serialize = false)
    {
        $response = HttpUtil::curl(self::ENDPOINT_BASE . $endpoint, $data, HttpUtil::METHOD_GET);
        return $serialize ?  $response : json_decode($response);
    }

    public function post(string $endpoint, array $data = [], $serialize = false)
    {
        $response = HttpUtil::curl(self::ENDPOINT_BASE . $endpoint, $data, HttpUtil::METHOD_POST);
        return $serialize ?  $response : json_decode($response);
    }
}