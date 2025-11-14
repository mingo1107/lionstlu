<?php

namespace frontend\controllers;


use ball\util\HttpUtil;
use ball\util\Url;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class FrontendController extends Controller
{
    protected $queryString;
    protected $title;
    protected $actionLabel;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['/site/logout', 'signup'],
                'denyCallback' => function () {
                    return $this->redirect(['/site/index']);
                },
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
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
        if (parent::beforeAction($action)) {
            if (!YII_DEBUG && HttpUtil::port() == 80) {
                Yii::$app->response->redirect(Url::base('https') . Url::current(), 301);
            }
            $this->queryString = HttpUtil::buildQuery($_GET);
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
        $params['qs'] = $this->queryString;
        $params['title'] = $this->title;
        $params['actionLabel'] = $this->actionLabel;
        return parent::render($view, $params);
    }

    public function kick()
    {
        Yii::$app->user->logout();
        Yii::$app->session->destroy();
        header('Location: /site/login', true, 302);
        exit;
    }
}