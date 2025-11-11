<?php

namespace ball\filter;


use ball\api\ResponseCode;
use HttpException;
use yii\base\ActionFilter;

class RequestFilter extends ActionFilter
{
    public $error = ['error' => ['message' => 'Auth error']];
    public $allow = [];

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            return $this->validateParams($action);
        }
        return false;
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }

    private function validateParams($action)
    {
        if (isset($this->allow[$action->id]['method'])) {
            $method = $this->allow[$action->id]['method'];
            if ($method == "get") {
                $data = $_GET;
            } else if ($method == "post") {
                $data = $_POST;
            } else {
                throw new HttpException("Unknown http method");
            }
            foreach ($this->allow[$action->id]["params"] as $p) {
                if (!isset($data[$p])) {
                    return $this->denyCallback("Lacks some parameters", ResponseCode::ERROR_LACK_PARAMS);
                }
            }
        }
        return true;
    }

    protected function denyCallback(string $errorMessage = null, string $code = null)
    {
        if (!empty($errorMessage)) {
            $this->error['error']['message'] = $errorMessage;
        }
        if (!empty($code)) {
            $this->error['error']['code'] = $code;
        }
        echo json_encode($this->error);
        return false;
    }
}