<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use yii\web\NotFoundHttpException;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    public static function collectionName()
    {
        return ['yii2db', 'user'];
    }

    public function attributes()
    {
        return [
            '_id',
            'id',
            'username',
            'password',
            'authKey',
            'accessToken',
            'first_name',
            'last_name',
            'email',
            'registration_datetime',
        ];
    }


    // public function fields()
    // {
    //     $fields = parent::fields();
    //     unset($fields['password'], $fields['_id'], $fields['id']); // optional
    //     return $fields;
    // }

    public function rules()
    {
        return [
            [['username', 'password', 'first_name', 'last_name', 'email'], 'required'],
            ['username', 'string', 'min' => 4, 
                'message' => '{attribute} must have at least 4 chars length.',
            ],
            ['password', 'match', 
                'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[_\-\.,]).{6,}$/',
                'message' => 'Password must be at least 6 characters long and contain at least one letter, one digit, and one special character (_ - . ,)',
            ],
            [['first_name', 'last_name'], 'match', 
                'pattern' => '/^[A-Z][a-z]*$/',
                'message' => '{attribute} must start with a capital letter and contain only lowercase letters afterward.',
            ],
            ['email', 'email'],
            [['username', 'password', 'first_name', 'last_name', 'email'], 'safe'],
        ];
    }
    


    public static function findIdentity($id)
    {
        $user = static::findOne(['id' => (int)$id]);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }
        return $user;
    }

    public static function validateId($id)
    {
        return static::findIdentity($id)->id;
    }

    public static function querySearch($query, $search)
    {
        return $query->andFilterWhere(['or',
            ['like', 'first_name', $search],
            ['like', 'last_name', $search],
            ['like', 'email', $search],
        ]);
    }


    public static function findIdentityByAccessToken($token, $type = null)
    {
        try {
            $decoded = JWT::decode($token, new Key(Yii::$app->params['jwtSecretKey'], 'HS256'));
            return static::findOne(['id' => $decoded->uid]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }


    /**
     * Override beforeSave to auto-increment `id` on insert
     */
    public function beforeSave($insert)
    {
        if ($insert && $this->id === null) {
            $last = static::find()->orderBy(['id' => SORT_DESC])->one();
            $this->id = $last ? $last->id + 1 : 1;
        }

        if ($this->isAttributeChanged('password')) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        }

        return parent::beforeSave($insert);
    }
}
