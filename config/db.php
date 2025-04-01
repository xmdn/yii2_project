<?php

return [
    // 'class' => 'yii\db\Connection',
    // 'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'class' => \yii\mongodb\Connection::class,
    'dsn' => 'mongodb://rootdb:toor022@mongo:27017/yii2db?authSource=admin',
    // 'username' => 'root',
    // 'password' => '',
    // 'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
