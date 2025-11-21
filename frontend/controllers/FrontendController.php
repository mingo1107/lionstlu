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

    /**
     * 檢查會員訪問權限
     * 條件：1. 已登入 2. 已開通 3. 在有效期限內
     * 
     * @return bool|Response true 表示有權限，否則返回重定向響應
     */
    protected function checkMemberAccess()
    {
        // 1. 檢查是否登入
        if (Yii::$app->user->isGuest) {
            // 未登入，儲存當前 URL 並重定向到登入頁
            Yii::$app->user->setReturnUrl(Yii::$app->request->url);
            Yii::$app->session->setFlash('warning', '請先登入以查看此內容');
            return $this->redirect(['/member/login']);
        }

        /** @var \common\models\MemberModel $member */
        $member = Yii::$app->user->identity;

        // 2. 檢查是否已開通
        if (!$member->isValidated()) {
            Yii::$app->session->setFlash('error', '您的會員帳號尚未開通，請聯絡管理員');
            return $this->redirect(['/member/center']);
        }

        // 3. 檢查是否在有效期限內
        if (!$member->isInValidPeriod()) {
            $message = $member->getAccessStatus();
            Yii::$app->session->setFlash('error', $message . '，如需延長請聯絡管理員');
            return $this->redirect(['/member/center']);
        }

        return true;
    }
}