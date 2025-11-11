<?php

namespace backend\filter;

use backend\controllers\BackendController;
use common\models\AccessModel;
use common\models\UserModel;
use Yii;
use yii\base\ActionFilter;

class AccessUserFilter extends ActionFilter
{
    public $except = [];

    public function beforeAction($action)
    {
        // filter except
        foreach ($this->except as $except) {
            $pattern = explode("/", $except);
            if (isset($pattern[1]) && $pattern[1] == '*') {
                if ($action->controller->id == $pattern[0]) {
                    return true;
                }
            }

            if (in_array($action->uniqueId, $this->except)) {
                return true;
            }
        }
        $access = AccessModel::findOnlineByPattern($action->controller->id);
        if (empty($access)) {
            /**
             * @var $controller BackendController
             */
            $controller = $action->controller;
            $controller->kick();
        }
        /**
         * @var $identity UserModel
         */
        $identity = yii::$app->user->getIdentity();
        $accessList = $identity->getAccessList();
        // TODO validate functions precisely by patterns
        if (isset($accessList[$access->id])) {
            if (in_array("*", $accessList[$access->id])) {
                return true;
            }
        }
        /**
         * @var $controller BackendController
         */
        $controller = $action->controller;
        $controller->kick();
        return false;
    }
}