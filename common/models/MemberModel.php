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
    const SCENARIO_IMPORT = 'import';

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
    
    /**
     * @var string 臨時儲存的 AuthKey（用於登入時）
     */
    private $_tempAuthKey;

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
        array_push(
            $rules,
            [['password_hash', 'email', 'mobile', 'city', 'district', 'zip', 'name'], 'required', 'on' => self::SCENARIO_CREATE],
            [['password', 'member_code'], 'safe', 'on' => static::SCENARIO_CREATE],
            [['password_hash', 'email', 'mobile', 'city', 'district', 'mobile', 'zip', 'name'], 'required', 'on' => self::SCENARIO_UPDATE],
            [['password', 'password2', 'member_code'], 'safe', 'on' => static::SCENARIO_UPDATE],
            [['member_code'], 'unique', 'targetAttribute' => 'member_code', 'filter' => function($query) {
                if (!$this->isNewRecord) {
                    $query->andWhere(['!=', 'id', $this->id]);
                }
            }, 'skipOnEmpty' => true, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['username', 'name', 'area_id'], 'required', 'on' => self::SCENARIO_SIGNUP],
            [['password', 'area_id'], 'safe', 'on' => static::SCENARIO_SIGNUP],
            [['password'], 'required', 'on' => self::SCENARIO_PASSWORD_RESET],
            [['email', 'name'], 'required', 'on' => self::SCENARIO_IMPORT],
            [['password', 'mobile', 'city', 'district', 'zip', 'birthday', 'address', 'other_city', 'period_start', 'period_end', 'area_id', 'member_code'], 'safe', 'on' => self::SCENARIO_IMPORT]
        );
        return $rules;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            // 統一處理 member_code 的補零（在所有場景下）
            $this->normalizeMemberCode();
            
            if ($this->scenario == self::SCENARIO_CREATE || $this->scenario == self::SCENARIO_SIGNUP) {
                // 自動使用 email 作為 username
                if (!empty($this->email)) {
                    $this->username = $this->email;
                }
                $this->generatePasswordResetToken();
                $this->generateRegisterToken(); // 產生註冊驗證 token
                // member_code 不再自動產生，由後端人員手動輸入
                $this->country = '台灣';
                $this->status = self::STATUS_ONLINE;
                $this->validate = self::VALIDATE_NO;
                // 確保 area_id 有值（註冊時必填，不能為 0）
                if (empty($this->area_id) || $this->area_id == '0') {
                    $this->area_id = null; // 讓驗證規則處理必填錯誤
                }
                $this->create_time = new Expression('now()');
            } else if ($this->scenario == self::SCENARIO_IMPORT) {
                // 匯入場景：自動使用 email 作為 username
                if (!empty($this->email)) {
                    $this->username = $this->email;
                }
                // 如果是新會員（沒有 ID），才設定預設值
                if ($this->isNewRecord) {
                    // member_code 不再自動產生，直接使用 Excel 檔案內容
                    $this->generatePasswordResetToken();
                    $this->country = '台灣';
                    $this->status = self::STATUS_ONLINE;
                    $this->validate = self::VALIDATE_YES; // 匯入的會員直接設為已認證
                    $this->create_time = new Expression('now()');
                } else {
                    $this->update_time = new Expression('now()');
                }
            } else if ($this->scenario == self::SCENARIO_UPDATE) {
                // 如果密碼有輸入，且長度 >= 8 且兩次輸入一致，則更新密碼
                // 如果密碼為空，則不更新密碼
                if (!empty(trim($this->password))) {
                    if (strlen(trim($this->password)) >= 8 && $this->password === $this->password2) {
                        $this->setPassword($this->password);
                    }
                }
                // 如果 area_id 為空或 NULL，設為 0
                if (empty($this->area_id) && $this->area_id !== '0') {
                    $this->area_id = 0;
                }
                $this->update_time = new Expression('now()');
            } else if ($this->scenario == static::SCENARIO_PASSWORD_RESET) {
                $this->setPassword($this->password);
            }
            
            return true;
        } else {
            return false;
        }
    }

    /**
     * 正規化會員編號：自動補零到四位數
     * 例如：1 -> 0001, 12 -> 0012, 123 -> 0123, 1234 -> 1234
     */
    private function normalizeMemberCode()
    {
        // 如果 member_code 不為空，且是純數字，則補零到四位數
        if (!empty($this->member_code) && is_numeric($this->member_code)) {
            // 轉換為整數再補零，避免小數點問題
            $code = intval($this->member_code);
            // 補零到四位數
            $this->member_code = str_pad($code, 4, '0', STR_PAD_LEFT);
        }
    }

    /**
     * 發送忘記密碼通知信
     */
    public function sendForgotPasswordNotice()
    {
        // 確保有 password_reset_token
        if (empty($this->password_reset_token)) {
            $this->generatePasswordResetToken();
            $this->save(false);
        }

        $params = Yii::$app->params;
        $resetLink = Yii::$app->urlManager->createAbsoluteUrl([
            'member/reset-password',
            'token' => $this->password_reset_token
        ]);

        $message = Yii::$app->mailer->compose('forgot-password-html', [
            'member' => $this,
            'resetLink' => $resetLink,
        ])
            ->setTo($this->email)
            ->setFrom([$params['smtp']['fromEmail'] => $params['smtp']['fromName']])
            ->setSubject('[台灣獅子大學] 帳號密碼重置信');

        return $message->send();
    }

    /**
     * 發送註冊驗證信
     */
    public function sendRegisterVerificationEmail()
    {
        $params = Yii::$app->params;
        $verifyLink = Yii::$app->urlManager->createAbsoluteUrl([
            'member/verify-email',
            'token' => $this->register_token
        ]);

        $message = Yii::$app->mailer->compose('register-verification-html', [
            'member' => $this,
            'verifyLink' => $verifyLink,
        ])
            ->setTo($this->email)
            ->setFrom([$params['smtp']['fromEmail'] => $params['smtp']['fromName']])
            ->setSubject('[台灣獅子大學] 會員註冊驗證信');

        return $message->send();
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
        $sql = "SELECT m.*, a.area_name FROM member m LEFT JOIN area a ON m.area_id = a.id WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (m.name like :keyword OR m.username like :keyword OR m.email like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (isset($search['status']) && $search['status'] !== '') {
            $sql .= " and m.status = :status";
            $params[":status"] = $search['status'];
        }

        if (isset($search['area_id']) && $search['area_id'] !== '') {
            $sql .= " and m.area_id = :area_id";
            $params[":area_id"] = $search['area_id'];
        }

        // 自行註冊判斷：前台註冊的會員通常會有 register_token
        // 如果 is_self_register = 1，表示自行註冊（前台註冊，有 register_token）
        // 如果 is_self_register = 0，表示後台建立（沒有 register_token 或 register_token 為空）
        if (isset($search['is_self_register']) && $search['is_self_register'] !== '') {
            if ($search['is_self_register'] == '1') {
                // 自行註冊：有 register_token 的會員
                $sql .= " and m.register_token IS NOT NULL AND m.register_token != ''";
            } else if ($search['is_self_register'] == '0') {
                // 後台建立：沒有 register_token 或 register_token 為空的會員
                $sql .= " and (m.register_token IS NULL OR m.register_token = '')";
            }
        }

        $sql .= " order by m.id desc ";
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
        $sql = "SELECT count(*) FROM member m WHERE 1 = 1 ";
        $params = [];
        if (!empty($search['keyword'])) {
            $sql .= " and (m.name like :keyword OR m.username like :keyword OR m.email like :keyword)";
            $params[":keyword"] = "%" . $search['keyword'] . "%";
        }

        if (isset($search['status']) && $search['status'] !== '') {
            $sql .= " and m.status = :status";
            $params[":status"] = $search['status'];
        }

        if (isset($search['area_id']) && $search['area_id'] !== '') {
            $sql .= " and m.area_id = :area_id";
            $params[":area_id"] = $search['area_id'];
        }

        // 自行註冊篩選
        if (isset($search['is_self_register']) && $search['is_self_register'] !== '') {
            if ($search['is_self_register'] == '1') {
                // 自行註冊：有 register_token 的會員
                $sql .= " and m.register_token IS NOT NULL AND m.register_token != ''";
            } else if ($search['is_self_register'] == '0') {
                // 後台建立：沒有 register_token 或 register_token 為空的會員
                $sql .= " and (m.register_token IS NULL OR m.register_token = '')";
            }
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
        return self::getDb()->createCommand(
            "SELECT 1 FROM member WHERE username = :username",
            [":username" => $username]
        )->queryScalar() == 1
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

    /**
     * 產生註冊驗證 token
     */
    public function generateRegisterToken()
    {
        // 使用不帶參數的版本以確保與 Yii2 2.0.6 兼容
        // 預設長度為 32，如果返回的長度不足則重複生成
        $token = Yii::$app->security->generateRandomString();
        if (strlen($token) < 32) {
            $token .= Yii::$app->security->generateRandomString();
        }
        $this->register_token = substr($token, 0, 32);
    }

    /**
     * 移除註冊驗證 token
     */
    public function removeRegisterToken()
    {
        $this->register_token = null;
    }

    /**
     * 產生會員編號（5位數字：00001~99999）
     */
    public function generateMemberCode()
    {
        // 如果已經有會員編號，就不再產生
        if (!empty($this->member_code)) {
            return;
        }

        try {
            // 找出目前最大的會員編號
            $maxCode = self::getDb()->createCommand(
                "SELECT MAX(CAST(member_code AS UNSIGNED)) as max_code FROM member WHERE member_code REGEXP '^[0-9]+$'"
            )->queryScalar();

            // 如果沒有任何會員編號，從 1 開始
            $nextNumber = empty($maxCode) ? 1 : intval($maxCode) + 1;

            // 如果超過 99999，從 1 重新開始（循環使用）
            if ($nextNumber > 99999) {
                $nextNumber = 1;
            }

            // 格式化為 5 位數字（前面補 0）
            $this->member_code = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // 檢查是否重複（雖然機率很低，但還是檢查一下）
            $attempts = 0;
            while (self::find()->where(['member_code' => $this->member_code])->exists() && $attempts < 100) {
                $nextNumber++;
                if ($nextNumber > 99999) {
                    $nextNumber = 1;
                }
                $this->member_code = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                $attempts++;
            }
        } catch (\Exception $e) {
            // 如果發生錯誤，使用時間戳作為備用方案
            $this->member_code = str_pad(substr(time(), -5), 5, '0', STR_PAD_LEFT);
        }
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
     * 取得關聯的區域
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(AreaModel::class, ['id' => 'area_id']);
    }
}
