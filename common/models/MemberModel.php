<?php

namespace common\models;


use ball\util\HttpUtil;
use PDO;
use Yii;
use yii\base\NotSupportedException;
use yii\db\Expression;
use yii\web\IdentityInterface;

class MemberModel extends Member implements IdentityInterface
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_SIGNUP = 'signup';
    const SCENARIO_PASSWORD_RESET = 'password_reset';

    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;

    const VALIDATE_NO = 0;
    const VALIDATE_YES = 1;

    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';

    // 20 hours
    const LOGIN_DURATION = 72000;
    const LOGIN_KEY_SECRET = '_mks_';

    public $password;
    public $password2;

    public static $statusLabel = [
        self::STATUS_ONLINE => '上線',
        self::STATUS_OFFLINE => '下線'
    ];

    public static $validateLabel = [
        self::VALIDATE_NO => '尚未認證',
        self::VALIDATE_YES => '已認證'
    ];

    public static $genderLabel = [
        self::GENDER_MALE => '男',
        self::GENDER_FEMALE => '女'
    ];

    public function rules()
    {
        $rules = parent::rules();
        array_push($rules,
            [['username', 'password_hash', 'email', 'mobile', 'city', 'district', 'mobile', 'zip', 'name'], 'required', 'on' => self::SCENARIO_CREATE],
            [['password'], 'safe', 'on' => static::SCENARIO_CREATE],
            [['password_hash', 'email', 'mobile', 'city', 'district', 'mobile', 'zip', 'name'], 'required', 'on' => self::SCENARIO_UPDATE],
            [['password', 'password2'], 'safe', 'on' => static::SCENARIO_UPDATE],
            [['username', 'name'], 'required', 'on' => self::SCENARIO_SIGNUP],
            [['password'], 'safe', 'on' => static::SCENARIO_SIGNUP],
            [['password'], 'required', 'on' => self::SCENARIO_PASSWORD_RESET]
        );
        return $rules;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->scenario == self::SCENARIO_CREATE || $this->scenario == self::SCENARIO_SIGNUP) {
                $this->generatePasswordResetToken();
                $this->country = '台灣';
                $this->status = self::STATUS_ONLINE;
                $this->validate = self::VALIDATE_NO;
                $this->create_time = new Expression('now()');
            } else if ($this->scenario == self::SCENARIO_UPDATE) {
                if (strlen(trim($this->password)) >= 8 && $this->password === $this->password2) {
                    $this->setPassword($this->password);
                }
                $this->update_time = new Expression('now()');
            } else if ($this->scenario == static::SCENARIO_PASSWORD_RESET) {
                $this->setPassword($this->password);
                $this->generatePasswordResetToken();
                $this->update_time = new Expression('now()');
            }
            return true;
        } else {
            return false;
        }
    }

    public function sendForgotPasswordNotice()
    {
        $content = "因為您再愛分享的網站點了重至密碼，所以收到這封信件，
請點<a href='https://lionstlu.org.tw/member/reset-password?token=$this->password_reset_token'>重設密碼連結</a>重設密碼<br/>若這不是你操作的，請忽略此信件";
        Yii::$app->mailer->compose()
            ->setTo($this->email)
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setSubject('[愛分享] 帳號密碼重置信')
            ->setHtmlBody($content)
            ->send();
    }

    /**
     * @param array $search
     * @param int|null $limit
     * @param int|null $offset
     * @return static[]
     * @throws \yii\db\Exception
     */
    public static function query(array $search, int $limit = null, int $offset = null)
    {
        $sql = "SELECT * FROM member WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (name like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if ($search['status'] !== '') {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }
        $sql .= " order by id desc ";
        if ($limit !== null) {
            $sql .= " limit $limit";
        }
        if ($offset !== null) {
            $sql .= " offset $offset";
        }
        return self::getDb()->createCommand($sql, $params)->queryAll(PDO::FETCH_OBJ);
    }

    /**
     * @param array $search
     * @return int
     * @throws \yii\db\Exception
     */
    public static function count(array $search = [])
    {
        $sql = "SELECT count(*) FROM member WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and name like :keyword";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (!empty($search['status'])) {
            $sql .= " and status = :status";
            $params[":status"] = $search['status'];
        }
        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }

    /**
     * @param string $username
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function exists(string $username)
    {
        return self::getDb()->createCommand("SELECT 1 FROM member WHERE username = :username",
            [":username" => $username])->queryScalar() == 1
            ? true : false;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ONLINE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ONLINE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ONLINE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return HttpUtil::getCookie(self::LOGIN_KEY_SECRET);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        $keyArray = explode("_", $authKey);
        if (count($keyArray) != 4) {
            return false;
        }

        if (is_numeric($keyArray[0])
            && intval($keyArray[0]) > 0
            && intval($keyArray[0]) <= time()
            && $keyArray[1] == $this->getId()
            && $keyArray[3] == $_SERVER['HTTP_USER_AGENT']
            && $keyArray[2] == HttpUtil::ip()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }


    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function logout()
    {
        HttpUtil::deleteCookie(self::LOGIN_KEY_SECRET);
        HttpUtil::deleteAllCookies();
    }

    public function applyLoginInfo()
    {
        $this->generateAuthKey();
    }

    public function generateAuthKey()
    {
        $authKey = sprintf("%s_%s_%s_%s", time(), $this->getId(), HttpUtil::ip(), $_SERVER['HTTP_USER_AGENT']);
        HttpUtil::setCookie(self::LOGIN_KEY_SECRET, $authKey, time() + self::LOGIN_DURATION);
    }
}