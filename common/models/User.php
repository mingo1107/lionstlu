<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $name
 * @property int $status
 * @property int $role
 * @property string $birthday
 * @property string $zip
 * @property string $country
 * @property string $city
 * @property string $district
 * @property string $address
 * @property string $mobile
 * @property string $create_time
 * @property string $update_time
 * @property int $login_count
 * @property string $last_login_ip
 * @property string $last_login_time
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'status', 'role', 'login_count'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'name', 'mobile', 'create_time'], 'required'],
            [['birthday', 'create_time', 'update_time', 'last_login_time'], 'safe'],
            [['username', 'country', 'city', 'district', 'mobile'], 'string', 'max' => 64],
            [['password_hash', 'password_reset_token', 'email'], 'string', 'max' => 256],
            [['name', 'address'], 'string', 'max' => 128],
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
            'parent_id' => 'Parent ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'name' => 'Name',
            'status' => 'Status',
            'role' => 'Role',
            'birthday' => 'Birthday',
            'zip' => 'Zip',
            'country' => 'Country',
            'city' => 'City',
            'district' => 'District',
            'address' => 'Address',
            'mobile' => 'Mobile',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'login_count' => 'Login Count',
            'last_login_ip' => 'Last Login Ip',
            'last_login_time' => 'Last Login Time',
        ];
    }
}
