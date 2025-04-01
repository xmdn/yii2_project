<?php

require __DIR__ . '/vendor/autoload.php';

$client = new \MongoDB\Client('mongodb://rootdb:toor022@mongo:27017/yii2db?authSource=admin');

$db = $client->selectDatabase('yii2db');
$collections = $db->listCollections();

foreach ($collections as $collection) {
    echo "Found collection: " . $collection->getName() . PHP_EOL;
}
