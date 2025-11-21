<?php

namespace frontend\controllers;


use ball\facebook\FacebookGraph;
use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\util\HttpUtil;
use ball\util\StringUtil;
use ball\util\Url;
use common\models\AreaModel;
use common\models\CustomerServiceLogModel;
use common\models\CustomerServiceModel;
use common\models\MemberBindModel;
use common\models\MemberModel;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use frontend\models\LoginForm;
use frontend\models\MemberResetPasswordForm;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class MemberController extends FrontendController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'only' => ['center', 'service', 'reply'],
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            // 為登入和註冊動作禁用 CSRF 驗證（如果需要）
            // 注意：通常應該保持 CSRF 驗證啟用，這裡只是為了除錯
            // 'csrf' => [
            //     'class' => \yii\filters\CsrfFilter::class,
            //     'only' => ['login'],
            //     'except' => ['login'], // 如果需要禁用特定動作的 CSRF
            // ],
        ]);
    }

    public function actionLogin()
    {
        //        $this->module->layout = 'iframe';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // 處理註冊請求
        $signupModel = new MemberModel(['scenario' => MemberModel::SCENARIO_SIGNUP]);
        $showSignup = false; // 預設顯示登入表單

        if (Yii::$app->request->post('action') === 'signup' && $signupModel->load(Yii::$app->request->post())) {
            $signupModel->username = trim($signupModel->username);
            if (!MemberModel::exists($signupModel->username)) {
                $signupModel->email = $signupModel->username;
                $signupModel->setPassword($signupModel->password);
                if ($signupModel->save()) {
                    // 發送驗證信
                    try {
                        if ($signupModel->sendRegisterVerificationEmail()) {
                            HtmlHelper::setMessage('註冊成功！我們已發送驗證信至您的 Email，請收取驗證信後點擊連結完成驗證，即可登入。');
                        } else {
                            HtmlHelper::setMessage('註冊成功！但驗證信發送失敗，請聯繫客服協助處理。');
                        }
                    } catch (\Exception $e) {
                        HtmlHelper::setMessage('註冊成功！但驗證信發送失敗：' . $e->getMessage());
                    }
                    // 不自動登入，導向登入頁
                    return $this->redirect(['/member/login']);
                } else {
                    HtmlHelper::setError(Html::errorSummary($signupModel));
                    $showSignup = true; // 註冊失敗，顯示註冊表單
                }
            } else {
                HtmlHelper::setError("很抱歉，'{$signupModel->username}'已經被人使用，請選擇其他E-Mail");
                $showSignup = true; // 帳號已存在，顯示註冊表單
            }
            $signupModel->password = '';
        }

        // 處理登入請求
        $model = new LoginForm();
        if (Yii::$app->request->post('action') !== 'signup' && $model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                // 登入成功，重定向到首頁（避免刷新時重複提交）
                return $this->goHome();
            } else {
                // 登入失敗，清除密碼並顯示錯誤
                $model->password = '';
            }
        }

        // 取得區域列表供註冊表單使用
        $areaList = AreaModel::findAllForSelect();
        
        return $this->render('login', [
            'model' => $model,
            'signupModel' => $signupModel,
            'showSignup' => $showSignup, // 傳遞標記給視圖
            'areaList' => $areaList, // 區域列表
        ]);
    }

    public function actionXhrFbLogin()
    {
        $token = Yii::$app->request->post("token");
        $id = Yii::$app->request->post("id");
        if (empty($token) || empty($id)) {
            return $this->asJson(["code" => "500", "message" => "Lacks some params"]);
        }
        $bind = MemberBindModel::findOne(["platform_uid" => $id]);
        if (empty($bind)) {
            $fb = new FacebookGraph();
            $result = $fb->get("/me", [
                "access_token" => $token,
                //"fields" => "token_for_business,email,name,id"
                "fields" => "email,name,id"

            ]);
            if (isset($result->email) && !empty($result->email)) {
                $member = MemberModel::findOne(["email" => $result->email]);
                if (empty($member)) {
                    $member = new MemberModel();
                    $member->username = $email ?? MemberBindModel::PLATFORM_FACEBOOK . "_" . $id;
                    $member->setPassword(StringUtil::generateRandomString(128));
                    $member->email = $result->email ?? "";
                    $member->name = $result->name;
                    $member->status = MemberModel::STATUS_ONLINE;
                    $member->validate = MemberModel::VALIDATE_NO;
                }
            } else {
                $member = new MemberModel();
                $member->username = $email ?? MemberBindModel::PLATFORM_FACEBOOK . "_" . $id;
                $member->setPassword(StringUtil::generateRandomString(128));
                $member->email = $result->email ?? "";
                $member->name = $result->name;
                $member->status = MemberModel::STATUS_ONLINE;
                $member->validate = MemberModel::VALIDATE_NO;
            }
        } else {
            $member = MemberModel::findOne(["id" => $bind->user_id]);
            if (empty($member)) {
                return $this->asJson(["code" => "500", "message" => "登入失敗，找不到會員"]);
            }
        }
        $member->last_login_time = new Expression("now()");
        $member->last_login_ip = HttpUtil::ip();
        $member->login_count += 1;
        if ($member->save()) {
            if (empty($bind)) {
                $bind = new MemberBindModel();
                $bind->user_id = $member->id;
                $bind->platform_uid = $id;
                $bind->platform = MemberBindModel::PLATFORM_FACEBOOK;
                $bind->status = MemberModel::STATUS_ONLINE;
                if (!$bind->save()) {
                    return $this->asJson(["code" => "000", "message" => "success"]);
                }
            }
            Yii::$app->user->login($member);
            return $this->asJson(["code" => "000", "message" => "success"]);
        } else {
            return $this->asJson(["code" => "500", "message" => Html::errorSummary($member)]);
        }
    }

    public function actionSignup()
    {
        // 註冊功能已整合到 actionLogin 中，此方法保留用於向後兼容
        // 重定向到登入頁面（登入頁面包含註冊功能）
        return $this->redirect(['/member/login']);
    }

    public function actionForgetPassword()
    {
        //        $this->layout = 'iframe';
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/');
        }
        $id = Yii::$app->request->post('id');
        if (!empty($id)) {
            $model = MemberModel::findOne(['email' => $id]);
            if ($model != null) {
                $model->sendForgotPasswordNotice();
            }
            HtmlHelper::setMessage("密碼重置信已經寄出，請至會員信箱收信重設密碼");
            return $this->redirect('/member/login');
        } else {
            return $this->render('forget-password');
        }
    }

    public function actionResetPassword()
    {
        $model = new MemberResetPasswordForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->reset()) {
                HtmlHelper::setMessage("密碼重置成功");
                return $this->redirect('login');
            } else {
                HtmlHelper::setError("發生錯誤，密碼重置失敗");
                return $this->redirect(Yii::$app->request->referrer);
            }
        } else {
            $token = Yii::$app->request->get('token');

            $member = MemberModel::findOne(['password_reset_token' => $token]);

            if ($member == null) {
                HtmlHelper::setError("發生錯誤，請重新操作");
                return $this->redirect('login');
            } else {
                return $this->render('reset-password', [
                    'token' => $token,
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Email 驗證
     * 入口：/member/verify-email?token=...
     */
    public function actionVerifyEmail()
    {
        $token = Yii::$app->request->get('token');

        if (empty($token)) {
            HtmlHelper::setError('驗證連結無效，請重新操作');
            return $this->redirect(['/member/login']);
        }

        $member = MemberModel::findOne(['register_token' => $token]);

        if ($member == null) {
            HtmlHelper::setError('驗證連結無效或已過期，請重新註冊或聯繫客服');
            return $this->redirect(['/member/login']);
        }

        // 檢查是否已經驗證過
        if ($member->validate == MemberModel::VALIDATE_YES) {
            HtmlHelper::setMessage('您的 Email 已經驗證過了，可以直接登入');
            return $this->redirect(['/member/login']);
        }

        // 執行驗證
        $member->validate = MemberModel::VALIDATE_YES;
        $member->removeRegisterToken(); // 移除驗證 token
        $member->update_time = new \yii\db\Expression('now()');

        if ($member->save(false)) {
            HtmlHelper::setMessage('Email 驗證成功！您現在可以登入了。');
            return $this->redirect(['/member/login']);
        } else {
            HtmlHelper::setError('驗證失敗，請聯繫客服協助處理');
            return $this->redirect(['/member/login']);
        }
    }

    public function actionCenter()
    {
        $breadcrumbs = [
            ['url' => '/member/center', 'label' => '會員中心'],
            ['label' => '會員資料修改']
        ];
        /**
         * @var $member MemberModel
         */
        $member = Yii::$app->user->getIdentity();
        $member->scenario = MemberModel::SCENARIO_UPDATE;
        if ($member->load(Yii::$app->request->post())) {
            if (!empty($member->password) && $member->password == $member->password2) {
                $member->setPassword($member->password);
            }
            // 處理生日欄位：如果為空字串，設為 null
            if (empty($member->birthday)) {
                $member->birthday = null;
            }
            if ($member->save()) {
                HtmlHelper::setMessage('會員資料更新成功');
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                HtmlHelper::setMessage(Html::errorSummary($member));
            }
            $member->password = '';
        }
        return $this->render('center', ['breadcrumbs' => $breadcrumbs, 'member' => $member]);
    }

    public function actionService()
    {
        $breadcrumbs = [
            ['url' => '/member/center', 'label' => '會員中心'],
            ['label' => CustomerServiceModel::$categoryLabel[CustomerServiceModel::CATEGORY_ORDER]]
        ];
        $model = new CustomerServiceModel(['scenario' => CustomerServiceModel::SCENARIO_CREATE]);
        if ($model->load(Yii::$app->request->post())) {
            $model->category = CustomerServiceModel::CATEGORY_ORDER;
            if ($model->save()) {
                HtmlHelper::setMessage('您的請求已經成功送出，客服人員稍後會主動與您聯繫，<a href="/">點此回首頁</a>');
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                HtmlHelper::setError(Html::errorSummary($model));
            }
        }
        return $this->render('service', ['breadcrumbs' => $breadcrumbs, 'category' => CustomerServiceModel::CATEGORY_ORDER, 'model' => $model]);
    }

    public function actionReply()
    {
        $start = Pagination::getOffset();
        $breadcrumbs = [
            ['url' => '/member/center', 'label' => '會員中心'],
            ['label' => '最新客服回覆']
        ];
        $list = CustomerServiceModel::query([
            'member_id' => Yii::$app->user->getId(),
            'category' => CustomerServiceModel::CATEGORY_ORDER
        ]);
        $count = CustomerServiceModel::count(['member_id' => Yii::$app->user->getId()]);
        $logList = [];
        foreach ($list as $c) {
            $logList[$c->id] = CustomerServiceLogModel::findAll(['customer_service_id' => $c->id]);
        }
        return $this->render('reply', [
            'breadcrumbs' => $breadcrumbs,
            "start" => $start,
            'count' => $count,
            'list' => $list,
            'logList' => $logList
        ]);
    }

    public function actionFbLogin()
    {
        Yii::$app->session->open();
        $ref = Yii::$app->request->get("ref");
        Yii::$app->session->set("ref", $ref);
        $fb = new Facebook([
            'app_id' => Yii::$app->params["fbAppId"], // Replace {app-id} with your app id
            'app_secret' => Yii::$app->params["fbSecret"],
            'default_graph_version' => 'v4.0',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email', 'public_profile']; // Optional permissions
        $loginUrl = $helper->getLoginUrl("https://lionstlu.org.tw/member/fb-login-callback", $permissions);
        return $this->redirect($loginUrl);
    }

    public function actionFbLoginCallback()
    {
        Yii::$app->session->open();
        $fb = new Facebook([
            'app_id' => Yii::$app->params["fbAppId"], // Replace {app-id} with your app id
            'app_secret' => Yii::$app->params["fbSecret"],
            'default_graph_version' => 'v4.0',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        try {
            //            $accessToken = $helper->getAccessToken();
            $accessToken = $helper->getAccessToken("https://lionstlu.org.tw/member/fb-login-callback");
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            //            echo $e->getCode();
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        //var_dump($accessToken->getValue());exit;
        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                //                return $this->redirect($helper->getReRequestUrl("https://www.shallwe.com.tw/member/callback"));
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();
        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                exit;
            }
        }
        // User is logged in with a long-lived access token.
        // You can redirect them to a members-only page.
        //header('Location: https://example.com/members.php');
        $token = (string)$accessToken;
        //        $token = yii::$app->request->post("token");
        $fb = new FacebookGraph();
        $result = $fb->get("/me", [
            "access_token" => $token,
            //"fields" => "token_for_business,email,name,id"
            "fields" => "email,name,id"

        ]);
        $ref = Yii::$app->session->get("ref");
        if (empty($ref)) {
            $ref = "/site/index";
        }
        //$bind = MemberBindModel::findOne(["platform_uid" => $result->token_for_business,
        //"platform" => MemberBindModel::PLATFORM_FACEBOOK]);
        $bind = null;
        if (empty($bind)) {
            $user = new MemberModel(["scenario" => MemberModel::SCENARIO_SIGNUP]);
            $bind = new MemberBindModel();
            $bind->platform_uid = $result->id;
            $bind->status = MemberModel::STATUS_ONLINE;
            $bind->platform = MemberBindModel::PLATFORM_FACEBOOK;
        } else {
            $user = MemberModel::findOne(["id" => $bind->user_id]);
        }
        $user->username = isset($result->email) && !empty($result->email) ? $result->email : "facebook_" . $result->id;
        $user->email = isset($result->email) && !empty($result->email) ? $result->email : "";
        //        $user->mobile = isset($result->mobile_phone) && !empty($result->mobile_phone) ? $result->mobile_phone : "";
        $user->setPassword(StringUtil::generateRandomString(40));
        $user->name = $result->name;
        $user->status = MemberModel::STATUS_ONLINE;
        $user->last_login_time = new Expression("now()");
        $user->last_login_ip = HttpUtil::ip();
        $user->validate = MemberModel::VALIDATE_YES;
        $user->login_count += 1;
        if ($user->save()) {
            if (empty($bind)) {
                $bind->user_id = $user->id;
                if (!$bind->save()) {
                    HtmlHelper::setError(Html::errorSummary($bind));
                    return $this->redirect(Url::to("/member/login"));
                }
            }
            Yii::$app->user->login($user);
            return $this->redirect(Url::to($ref));
        } else {
            HtmlHelper::setError(Html::errorSummary($user));
            return $this->redirect(Url::to("/member/login"));
        }
    }
}
