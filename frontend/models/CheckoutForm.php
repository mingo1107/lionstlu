<?php

namespace frontend\models;


use yii\base\Model;

class CheckoutForm extends Model
{
    public $standardId;
    public $name;
    public $mobile;
    public $email;
    public $zip;
    public $city;
    public $district;
    public $address;

    public $receiver_standardId;
    public $receiver_name;
    public $receiver_mobile;
    public $receiver_email;
    public $receiver_zip;
    public $receiver_city;
    public $receiver_district;
    public $receiver_address;

    public $password;
    public $password2;

    public function rules()
    {
        return [
            [['email', 'name', 'mobile', 'email', 'zip', 'city', 'district', 'address',
                'receiver_email', 'receiver_name', 'receiver_mobile', 'receiver_email',
                'receiver_zip', 'receiver_city', 'receiver_district', 'receiver_address'], 'required'],
            [['password', 'password2'], 'safe']
        ];
    }
}