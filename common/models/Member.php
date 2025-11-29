<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "member".
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $name
 * @property int $status
 * @property int $validate 認證狀態
 * @property int $role
 * @property string $gender M or F
 * @property string $birthday
 * @property string $zip
 * @property string $country
 * @property string $city
 * @property string $district
 * @property string $address
 * @property string $mobile
 * @property string $other_city 其他城市
 * @property string $club_name 分會名稱
 * @property string $period_start 期間開始
 * @property string $period_end 期間結束
 * @property int $area_id 區域ID
 * @property string $create_time
 * @property string $update_time
 * @property int $login_count
 * @property string $last_login_ip
 * @property string $last_login_time
 */
class Member extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password_hash', 'email', 'name', 'create_time'], 'required'],
            [['status', 'validate', 'role', 'login_count', 'area_id'], 'integer'],
            [['birthday', 'period_start', 'period_end', 'create_time', 'update_time', 'last_login_time'], 'safe'],
            [['username', 'country', 'city', 'district', 'mobile'], 'string', 'max' => 64],
            [['password_hash', 'password_reset_token', 'email', 'other_city'], 'string', 'max' => 256],
            [['name', 'address', 'club_name'], 'string', 'max' => 128],
            [['gender'], 'string', 'max' => 2],
            [['zip'], 'string', 'max' => 8],
            [['last_login_ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'name' => 'Name',
            'status' => 'Status',
            'validate' => 'Validate',
            'role' => 'Role',
            'gender' => 'Gender',
            'birthday' => 'Birthday',
            'zip' => 'Zip',
            'country' => 'Country',
            'city' => 'City',
            'district' => 'District',
            'address' => 'Address',
            'mobile' => 'Mobile',
            'other_city' => '其他城市',
            'club_name' => '分會名稱',
            'period_start' => '期間開始',
            'period_end' => '期間結束',
            'area_id' => 'Area ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'login_count' => 'Login Count',
            'last_login_ip' => 'Last Login Ip',
            'last_login_time' => 'Last Login Time',
        ];
    }

    /**
     * 取得格式化的會員編號（補零到四位數）
     * @return string
     */
    public function getFormattedId()
    {
        return str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * 檢查會員是否已驗證（開通）
     * @return bool
     */
    public function isValidated()
    {
        return $this->validate == 1;
    }

    /**
     * 檢查會員是否在有效期限內
     * @return bool
     */
    public function isInValidPeriod()
    {
        $now = time();

        // 如果沒有設定期限，視為永久有效
        if (empty($this->period_start) && empty($this->period_end)) {
            return true;
        }

        // 檢查開始時間
        if (!empty($this->period_start)) {
            $startTime = strtotime($this->period_start);
            if ($now < $startTime) {
                return false; // 還未到開始時間
            }
        }

        // 檢查結束時間
        if (!empty($this->period_end)) {
            $endTime = strtotime($this->period_end . ' 23:59:59'); // 包含結束當天
            if ($now > $endTime) {
                return false; // 已過期
            }
        }

        return true;
    }

    /**
     * 檢查會員是否有權限訪問受保護內容
     * 條件：1. 已驗證 2. 在有效期限內
     * @return bool
     */
    public function hasAccessPermission()
    {
        return $this->isValidated() && $this->isInValidPeriod();
    }

    /**
     * 獲取會員狀態描述
     * @return string
     */
    public function getAccessStatus()
    {
        if (!$this->isValidated()) {
            return '會員尚未開通';
        }

        if (!$this->isInValidPeriod()) {
            if (!empty($this->period_start) && time() < strtotime($this->period_start)) {
                return '會員尚未生效（開始日期：' . $this->period_start . '）';
            }
            if (!empty($this->period_end) && time() > strtotime($this->period_end . ' 23:59:59')) {
                return '會員已過期（到期日期：' . $this->period_end . '）';
            }
        }

        return '會員有效';
    }

    /**
     * 獲取會員剩餘天數
     * @return int|null 剩餘天數，null 表示無期限或未設定
     */
    public function getRemainingDays()
    {
        if (empty($this->period_end)) {
            return null; // 無期限
        }

        $endTime = strtotime($this->period_end . ' 23:59:59');
        $now = time();

        if ($now > $endTime) {
            return 0; // 已過期
        }

        return ceil(($endTime - $now) / 86400); // 轉換為天數
    }
}
