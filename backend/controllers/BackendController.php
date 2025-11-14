<?php

namespace backend\controllers;


use backend\filter\AccessUserFilter;
use ball\util\HttpUtil;
use ball\util\Url;
use common\models\AccessModel;
use common\models\UserModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class BackendController extends Controller
{
    protected $queryString;
    protected $title;
    protected $actionLabel;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'logout'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            [
                'class' => AccessUserFilter::class,
                'except' => ["site/*", 'upload/*']
            ]
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!YII_DEBUG && HttpUtil::port() == 80) {
            Yii::$app->response->redirect(Url::base('https') . Url::current(), 301);
        }
        if (parent::beforeAction($action)) {
            $this->queryString = HttpUtil::buildQuery($_GET);
            $this->applyDefaultBreadcrumbsAndTitle($action);
            return true;
        } else {
            return false;
        }
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        Yii::$app->session->close();
        return $result;
    }

    public function render($view, $params = [])
    {
        if ($this->action->id != 'login') {
            $menuList = [];
            $menuParentList = AccessModel::findAllVisibleParent();
            foreach ($menuParentList as $parent) {
                $menuList[$parent->id] = AccessModel::findAllVisibleByParent($parent->id);
            }
            /**
             * @var $user UserModel
             */
            $user = Yii::$app->user->getIdentity();
            //echo json_encode($menuParentList);exit;
            Yii::$app->view->params['accessList'] = $user->getAccessList();
            Yii::$app->view->params['menuList'] = $menuList;
            Yii::$app->view->params['menuParentList'] = $menuParentList;
        }
        $params['qs'] = $this->queryString;
        $params['title'] = $this->title;
        $params['actionLabel'] = $this->actionLabel;
        return parent::render($view, $params);
    }

    protected function applyDefaultBreadcrumbsAndTitle($action)
    {
        $qs = HttpUtil::buildQuery($_GET, ['id']);
        $indexTitle = $this->actionLabel . '管理';
        if ($action->id == 'index') {
            $this->title = $indexTitle;
            $this->view->params['breadcrumbs'][] = ['label' => $indexTitle,
                'url' => ['/' . $action->controller->id . '/index']];
        } else if ($action->id == 'create') {
            $this->title = '建立' . $this->actionLabel;
            $this->view->params['breadcrumbs'][] = ['label' => $indexTitle,
                'url' => ['/' . $action->controller->id . '/index' . $qs]];
            $this->view->params['breadcrumbs'][] = ['label' => $this->title];
        } else if ($action->id == 'update') {
            $this->title = '編輯' . $this->actionLabel;
            $this->view->params['breadcrumbs'][] = ['label' => $indexTitle,
                'url' => ['/' . $action->controller->id . '/index' . $qs]];
            $this->view->params['breadcrumbs'][] = ['label' => $this->title];
        }
    }

    protected function applyBreadcrumbsAndTitle(string $title)
    {
        $this->title = $title;
        $qs = HttpUtil::buildQuery($_GET, ['id']);
        $indexTitle = $this->actionLabel . '管理';
        $this->view->params['breadcrumbs'][] = ['label' => $indexTitle,
            'url' => ['/' . Yii::$app->controller->id . '/index' . $qs]];
        $this->view->params['breadcrumbs'][] = ['label' => $title];
    }

    public function kick()
    {
        Yii::$app->user->logout();
        Yii::$app->session->destroy();
        header('Location: /site/login', true, 302);
        exit;
    }
}