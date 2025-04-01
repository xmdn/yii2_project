<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\mongodb\ActiveRecord;

class RegisterForm extends Model
{
    public $username;
    public $password;
    public $first_name;
    public $last_name;
    public $email;

    public function rules()
    {
        return [
            [['username', 'password', 'first_name', 'last_name', 'email'], 'required'],
            ['username', 'string', 'min' => 4],
            ['password', 'match', 'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[_\-\.,]).{6,}$/'],
            [['first_name', 'last_name'], 'match', 'pattern' => '/^[A-Z][a-z]*$/'],
            ['email', 'email'],
        ];
    }

    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->username = $this->username;
        $user->password = Yii::$app->security->generatePasswordHash($this->password);
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->email = $this->email;
        $user->registration_datetime = date('d-m-Y H:i');

        return $user->save();
    }
}
