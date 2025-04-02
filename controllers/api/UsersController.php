<?php

namespace app\controllers\api;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\User;
use app\models\Task;
use app\components\BaseApiController;
use app\traits\ApiControllerTrait;

class UsersController extends BaseApiController
{
    use ApiControllerTrait;

    public $modelClass = 'app\models\User';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['delete']);

        $actions['index']['prepareDataProvider'] = function () {
            $query = User::find();
            return $this->createPaginatedProvider($query, ['first_name', 'last_name', 'email']);
        };

        return $actions;
    }

    protected function ifRowExist($user, $data)
    {
        foreach (['username', 'first_name', 'last_name', 'email', 'password'] as $field) {
            if (isset($data[$field]) && $user->{$field} !== $data[$field]) {
                $user->{$field} = $data[$field];
            }
        }
        return $user;
    }

    public function actionUpdate($id)
    {
        $user = User::findOne(['id' => (int)$id]);

        if (!$user) {
            throw new NotFoundHttpException("User not found.");
        }

        $data = Yii::$app->request->getBodyParams();

        $user = $this->ifRowExist($user, $data);

        if ($user->save()) {
            return ['message' => 'User updated successfully.', 'user' => $user];
        }

        return ['error' => $user->getErrors()];
    }


    public function actionFind($id)
    {
        $model = User::findOne(['id' => (int)$id]);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException("User not found: $id");
    }


    public function actionDelete($id)
    {
        $user = User::findOne(['id' => (int)$id]);
        
        if (!$user) {
            throw new NotFoundHttpException("User not found.");
        }

        // Delete all related tasks
        Task::deleteAll(['user_id' => $user->id]);

        if ($user->delete()) {
            return ['message' => 'User and related tasks deleted successfully.'];
        }

        return [
            'error' => 'Failed to delete the user.'
        ];
    }
}
