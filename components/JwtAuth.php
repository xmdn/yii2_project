<?php

namespace app\components;

use Yii;
use yii\filters\auth\AuthInterface;
use yii\web\UnauthorizedHttpException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use yii\filters\auth\AuthMethod;

class JwtAuth extends AuthMethod
{
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            return null;
        }

        try {
            $decoded = JWT::decode($matches[1], new Key(Yii::$app->params['jwtSecretKey'], 'HS256'));
            $identity = \app\models\User::findIdentity($decoded->uid);
            if (!$identity) {
                throw new UnauthorizedHttpException('User not found');
            }

            return $identity;
        } catch (\Throwable $e) {
            throw new UnauthorizedHttpException('Invalid token: ' . $e->getMessage());
        }
    }

    public function challenge($response)
    {
        $response->getHeaders()->set('WWW-Authenticate', 'Bearer realm="api"');
    }

    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Unauthorized');
    }
}
