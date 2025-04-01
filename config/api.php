<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

return [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\controllers\api',
    'bootstrap' => ['log'],
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'cookieValidationKey' => 'some-api-cookie-secret',
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'db' => $db,
        'mongodb' => require __DIR__ . '/db.php',
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['users' => 'users'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET <id>' => 'find',
                        'PUT <id>' => 'update',
                        'DELETE <id>' => 'delete',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['users' => 'auth'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'register',
                        'POST login' => 'login',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['users' => 'user-task'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET <id>/tasks' => 'index',
                        'POST <id>/tasks' => 'create',
                        'GET <id>/tasks/<taskId>' => 'view',
                        'PUT <id>/tasks/<taskId>' => 'update',
                        'DELETE <id>/tasks/<taskId>' => 'delete',
                        'DELETE <id>/tasks' => 'delete-all',
                        'GET <id>/tasks/stats' => 'stats',
                        'GET <id>/stats-global' => 'global-stats',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
