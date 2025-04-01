<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/console.php'; // ← this path matches the basic template
$app = new yii\console\Application($config);

$collections = Yii::$app->mongodb->getDatabase()->listCollections();
$names = array_map(fn($c) => $c['name'], iterator_to_array($collections));

if (in_array('user', $names)) {
    Yii::$app->mongodb->getCollection('user')->drop();
    echo "✅ Dropped 'user' collection.\n";
} else {
    echo "ℹ️ 'user' collection does not exist.\n";
}

if (in_array('task', $names)) {
    Yii::$app->mongodb->getCollection('task')->drop();
    echo "✅ Dropped 'task' collection.\n";
} else {
    echo "ℹ️ 'task' collection does not exist.\n";
}
