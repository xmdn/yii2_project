<?php

namespace app\components;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

class BaseApiController extends ActiveController
{
    protected function createPaginatedProvider($query, $sortAttributes = [])
    {
        $queryParams = Yii::$app->request->queryParams;
        $search = $queryParams['query'] ?? null;

        $modelClass = $query->modelClass ?? null;

        if ($search && method_exists($modelClass, 'querySearch')) {
            $query = $modelClass::querySearch($query, $search);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $queryParams['per-page'] ?? 10,
                'pageParam' => 'page',
                'pageSizeParam' => 'per-page',
            ],
            'sort' => [
                'attributes' => $sortAttributes,
                'defaultOrder' => [$sortAttributes[0] ?? 'id' => SORT_ASC],
            ],
        ]);
    }

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
}
