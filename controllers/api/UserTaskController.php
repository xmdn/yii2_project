<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Task;
use app\models\User;
use app\components\BaseApiController;
use app\traits\ApiControllerTrait;

class UserTaskController extends BaseApiController
{
    use ApiControllerTrait;

    public $modelClass = 'app\models\Task';

    public function actionIndex($id)
    {
        $user = User::findOne(['id' => (int)$id]);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $tasks = Task::find()->where(['user_id' => $user->id])->all();

        return array_map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'created_at' => date('d-m-Y H:i', strtotime($task->created_at)),
            ];
        }, $tasks);
    }

    public function actionCreate($id)
    {
        $user = User::findOne(['id' => (int)$id]);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $task = new Task();
        $task->load(\Yii::$app->request->getBodyParams(), '');
        $task->user_id = (int) $user->id;
        $task->status = 'New';
        $task->created_at = date('d-m-Y H:i');

        if ($task->save()) {
            return $task;
        }

        return ['error' => $task->getErrors()];
    }

    public function actionView($id, $taskId)
    {
        $user = User::findOne(['id' => (int)$id]);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return Task::findOne(['user_id' => (int) $user->id, 'id' => (int) $taskId]);
    }

    public function actionUpdate($id, $taskId)
    {
        $user = User::findOne(['id' => (int)$id]);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $task = Task::findOne(['user_id' => (int) $user->id, 'id' => (int) $taskId]);
        if (!$task) throw new NotFoundHttpException('Task not found');

        $task->load(\Yii::$app->request->getBodyParams(), '');
        if ($task->save()) {
            return $task;
        }

        return ['error' => $task->getErrors()];
    }

    public function actionDelete($id, $taskId)
    {
        $user = User::findOne(['id' => (int)$id]);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $task = Task::findOne(['user_id' => (int) $user->id, 'id' => (int) $taskId, 'status' => 'New']);
        if (!$task) throw new NotFoundHttpException('Unprocessed task not found');

        return $task->delete();
    }

    public function actionDeleteAll($id)
    {
        $user = User::findOne(['id' => (int)$id]);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return Task::deleteAll(['user_id' => (int) $user->id, 'status' => 'New']);
    }

    public function actionStats($id)
    {
        $user = User::findOne(['id' => (int)$id]);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $result = Task::getCollection()->aggregate([
            ['$match' => ['user_id' => $user->id]],
            ['$group' => [
                '_id' => '$status',
                'count' => ['$sum' => 1]
            ]],
        ]);

        $stats = ['New' => 0, 'In Progress' => 0, 'Done' => 0];
        foreach ($result as $row) {
            $stats[$row['_id']] = $row['count'];
        }

        return $stats;
    }

    public function actionGlobalStats()
    {
        $pipeline = [
            ['$group' => [
                '_id' => ['user_id' => '$user_id', 'status' => '$status'],
                'count' => ['$sum' => 1]
            ]],
            ['$group' => [
                '_id' => '$_id.user_id',
                'statuses' => [
                    '$push' => [
                        'status' => '$_id.status',
                        'count' => '$count'
                    ]
                ]
            ]]
        ];

        $raw = Task::getCollection()->aggregate($pipeline);

        $users = User::find()->indexBy('id')->all();
        $output = [];

        foreach ($raw as $record) {
            $userId = $record['_id'];
            if (!isset($users[$userId])) continue;

            $user = $users[$userId];
            $stats = [
                'user_id' => $userId,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'New' => 0,
                'In Progress' => 0,
                'Done' => 0,
            ];

            foreach ($record['statuses'] as $status) {
                $stats[$status['status']] = $status['count'];
            }

            $output[] = $stats;
        }

        return $output;
    }
    
    public function actionUserStats($id)
    {
        $user = User::findOne(['id' => (int)$id]);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $statuses = ['New', 'In Progress', 'Done'];
        $result = [
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'New' => 0,
            'In Progress' => 0,
            'Done' => 0
        ];

        $pipeline = [
            ['$match' => ['user_id' => (int)$id]],
            ['$group' => [
                '_id' => '$status',
                'count' => ['$sum' => 1]
            ]],
        ];

        $aggregate = Task::getCollection()->aggregate($pipeline);

        foreach ($aggregate as $row) {
            $result[$row['_id']] = $row['count'];
        }

        return $result;
    }

    public function actionGlobalStatsObjects()
    {
        $users = User::find()->indexBy('id')->all();
        $tasks = Task::find()->asArray()->all();

        $grouped = [];
        foreach ($tasks as $task) {
            $uid = $task['user_id'];
            $status = $task['status'];

            if (!isset($users[$uid])) continue;

            if (!isset($grouped[$uid])) {
                $grouped[$uid] = [
                    'user_id' => $uid,
                    'first_name' => $users[$uid]->first_name,
                    'last_name' => $users[$uid]->last_name,
                    'New' => [],
                    'In Progress' => [],
                    'Done' => []
                ];
            }

            $grouped[$uid][$status][] = $task;
        }

        return array_values($grouped);
    }
}
