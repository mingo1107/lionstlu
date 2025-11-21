<?php

namespace frontend\models;


use ball\helper\HtmlHelper;
use ball\util\HttpUtil;
use common\models\MemberModel;
use Yii;
use yii\base\Model;
use yii\db\Expression;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_member;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getMember();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '帳號或密碼不正確');
            }
        }
    }


    public function login()
    {
        if ($this->validate()) {
            $user = $this->getMember();
            $user->last_login_time = new Expression('now()');
            $user->last_login_ip = HttpUtil::ip();
            $user->login_count += 1;
            $user->update();
            
            // 在 login() 之前先生成 AuthKey，確保 getAuthKey() 能返回正確的值
            $user->generateAuthKey();
            
            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        HtmlHelper::setError('帳號或密碼不正確');
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return MemberModel|null
     */
    protected function getMember()
    {
        if ($this->_member === null) {
            $this->_member = MemberModel::findByUsername($this->username);
        }

        return $this->_member;
    }
}