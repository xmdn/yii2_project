<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function actionLogin()
    {
        try {
            $request = Yii::$app->request;
            $username = $request->post('username');
            $password = $request->post('password');

            $user = User::findByUsername($username);
            if (!$user || !$user->validatePassword($password)) {
                return ['error' => 'Invalid credentials'];
            }

            $payload = [
                'iat' => time(),
                'exp' => time() + 3600,
                'uid' => $user->id,
            ];

            $jwt = JWT::encode(
                $payload,
                Yii::$app->params['jwtSecretKey'],
                'HS256'
            );

            return ['token' => $jwt];
        } catch (\Throwable $e) {
            return [
                'error' => 'Login failed',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
        }
    }


    public function actionRegister()
    {
        $request = Yii::$app->request;

        $user = new User();
        $user->username = $request->post('username');
        $user->password = Yii::$app->security->generatePasswordHash($request->post('password'));
        $user->first_name = $request->post('first_name');
        $user->last_name = $request->post('last_name');
        $user->email = $request->post('email');
        $user->registration_datetime = date('d-m-Y H:i');

        if ($user->save()) {
            return ['message' => 'User registered successfully'];
        }

        return ['error' => $user->getErrors()];
    }
}
