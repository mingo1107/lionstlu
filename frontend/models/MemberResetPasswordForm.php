<?php


namespace frontend\models;


use common\models\MemberModel;
use yii\base\Model;

class MemberResetPasswordForm extends Model
{
    public $token;
    public $password;
    public $password2;

    /* @var MemberModel $_user */
    private $_user;

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['token', 'password', 'password2'], 'required'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || $user->password_reset_token != $this->token || ($this->password != $this->password2)) {
                $this->addError("密碼必須一致且不能為空");
            }
            return true;
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function reset()
    {
        if ($this->validatePassword()) {
            $this->_user->password = $this->password;
            $this->_user->scenario = MemberModel::SCENARIO_PASSWORD_RESET;
            $this->_user->save();
            if ($this->_user->errors) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function success()
    {
    }

    /**
     * Finds user by [[username]]
     *
     * @return MemberModel|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = MemberModel::findOne(['password_reset_token' => $this->token]);
        }

        return $this->_user;
    }
}