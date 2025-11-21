<?php

namespace common\models;

use ball\util\HttpUtil;
use PDO;
use Yii;
use yii\base\NotSupportedException;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

class UserModel extends User implements IdentityInterface
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_ONLINE = 1;
    const STATUS_OFFLINE = 0;

    const ROLE_ADMIN = 1;
    const ROLE_VENDOR = 2;

    // 20 hours
    const LOGIN_DURATION = 72000;
    const LOGIN_KEY_SECRET = '_nks_';

    public $password;
    public $password2;
    
    /**
     * @var string 臨時儲存的 AuthKey（用於登入時）
     */
    private $_tempAuthKey;

    public static $statusLabel = [
        self::STATUS_ONLINE => '上線',
        self::STATUS_OFFLINE => '下線'
    ];

    public static $roleLabel = [
        self::ROLE_ADMIN => '管理員',
        self::ROLE_VENDOR => '供應商'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function beforeValidate()
    {
        if ($this->scenario == self::SCENARIO_CREATE || $this->scenario == self::SCENARIO_UPDATE) {
            if ($this->scenario == self::SCENARIO_CREATE) {
                $this->generatePasswordResetToken();
                $this->create_time = new Expression('now()');
            }
            $this->setPassword($this->password);
            $this->update_time = new Expression('now()');
        }
        return parent::beforeValidate();
    }

    public function rules()
    {
        $rules = parent::rules();
        array_push($rules,
            [['email', 'username', 'name', 'mobile'], 'required', 'on' => static::SCENARIO_CREATE],
            [['password'], 'safe', 'on' => static::SCENARIO_CREATE],
            [['email', 'name', 'mobile'], 'required', 'on' => static::SCENARIO_UPDATE],
            [['password'], 'safe', 'on' => static::SCENARIO_UPDATE]
        );
        return $rules;
    }


    /**
     * @param array $search
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws \yii\db\Exception
     */
    public static function queryAll(array $search, int $limit = null, int $offset = null)
    {
        $params = [];
        $sql = "SELECT m.*, r.name AS role_name FROM user m INNER JOIN access_user u ON m.id = u.user_id 
          LEFT OUTER JOIN access_role r ON u.role_id = r.id  WHERE 1 = 1 ";
        if (!empty($search['keyword'])) {
            $sql .= " and (m.id like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }
        if ($search['status'] !== '') {
            $sql .= " and m.status = :status";
            $params[":status"] = $search['status'];
        }
        $sql .= " order by m.id desc";
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
     * @return false|null|string
     * @throws \yii\db\Exception
     */
    public static function count(array $search = [])
    {
        $sql = "SELECT count(*) FROM user m INNER JOIN access_user u ON m.id = u.user_id 
          INNER JOIN access_role r ON u.role_id = r.id  WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and m.id like :keyword";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }
        if (!empty($search['status'])) {
            $sql .= " and m.status = :status";
            $params[":status"] = $search['status'];
        }
        return self::getDb()->createCommand($sql, $params)->queryScalar();
    }

    public function setAccessList(array $accessList)
    {
        // no op
    }

    /**
     * @return array
     */
    public function getAccessList()
    {
        $model = AccessUserModel::findOne(['user_id' => $this->getId()]);
        //echo  $this->getId().json_encode($model);exit;
        $accessList = [];
        if (!empty($model->access_list)) {
            $accessList = json_decode($model->access_list) ?? [];
            if (!empty($model->role_id)) {
                $role = AccessRoleModel::findOne(['id' => $model->role_id]);
                if (!empty($role->access_list)) {
                    $roleAccessLust = $role->access_list ?? [];
                    $accessList = ArrayHelper::merge((array)$accessList, json_decode($roleAccessLust));
                }
            }
        }
        return $accessList;
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
        // 優先返回臨時 AuthKey（用於登入時，因為 Cookie 在同一請求中不會立即更新）
        if ($this->_tempAuthKey !== null) {
            return $this->_tempAuthKey;
        }
        return HttpUtil::getCookie(self::LOGIN_KEY_SECRET);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        // 使用 | 分隔符，避免 User Agent 中的下劃線干擾（如 Mac OS X 10_15_7）
        $keyArray = explode("|", $authKey);
        if (count($keyArray) != 4) {
            return false;
        }

        if (is_numeric($keyArray[0])
            && intval($keyArray[0]) > 0
            && intval($keyArray[0]) <= time()
            && $keyArray[1] == $this->getId()
            && $keyArray[3] == $_SERVER['HTTP_USER_AGENT']) {
            // 移除 IP 驗證，因為可能因為 Proxy/Load Balancer 導致 IP 不一致
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

    /**
     * Generates password hash from password and sets it to the model
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates new password reset token
     * @throws \yii\base\Exception
     */
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
        // 使用 | 作為分隔符，因為 User Agent 可能包含下劃線（如 Mac OS X 10_15_7）
        $authKey = sprintf("%s|%s|%s|%s", time(), $this->getId(), HttpUtil::ip(), $_SERVER['HTTP_USER_AGENT']);
        
        // 設置 Cookie（瀏覽器在下一個請求才會帶上）
        HttpUtil::setCookie(self::LOGIN_KEY_SECRET, $authKey, time() + self::LOGIN_DURATION);
        
        // 同時儲存到臨時屬性（供同一請求中的 getAuthKey() 使用）
        $this->_tempAuthKey = $authKey;
    }

    /**
     * @return static[]
     */
    public static function findAllOnlineVendor()
    {
        return self::findAll(['status' => self::STATUS_ONLINE, 'role' => self::ROLE_VENDOR]);
    }
}
