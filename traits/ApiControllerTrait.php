<?php

namespace app\traits;

use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use app\components\JwtAuth;

trait ApiControllerTrait
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                ['class' => JwtAuth::class],
            ],
        ];

        return $behaviors;
    }
}
