<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\web\NotFoundHttpException;
use app\models\User;
use app\components\TaskNotFoundException;

class Task extends ActiveRecord
{
    const STATUS_NEW = 'New';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_DONE = 'Done';

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE,
        ];
    }

    public static function collectionName()
    {
        return 'task';
    }

    public function attributes()
    {
        return ['_id', 'id', 'user_id', 'title', 'status', 'created_at'];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['created_at'] = function () {
            return date('d-m-Y H:i', strtotime($this->created_at));
        };
        return $fields;
    }

    public function rules()
    {
        return [
            [['user_id', 'title'], 'required'],
            [['title'], 'string'],
            ['status', 'in', 'range' => self::getStatuses(), 
                'message' => 'Status is invalid. Allowed values: ' . implode(', ', self::getStatuses())
            ],
        ];
    }

    public static function querySearch($query, $search)
    {
        return $query->andFilterWhere(['or',
            ['like', 'title', $search],
            ['like', 'status', $search],
        ]);
    }

    public static function createForUser($userId, array $data): ?self
    {
        $task = new self();
        $task->load($data, '');
        $task->user_id = User::validateId((int)$userId);
        $task->created_at = date('d-m-Y H:i');

        return $task;
    }

    public static function getForUser($userId, $taskId, array $extra = []): self
    {
        $conditions = array_merge([
            'user_id' => User::validateId((int)$userId),
            'id' => (int)$taskId,
        ], $extra);
        
        $task = static::findOne($conditions);

        if (!$task) {
            if (isset($extra['status']) && $extra['status'] === self::STATUS_NEW) {
                throw TaskNotFoundException::forUnprocessed();
            }
    
            throw new TaskNotFoundException();
        }
    

        return $task;
    }

    public static function getForUserAll($userId): self
    {
        $tasks = static::find()->where(['user_id' => User::validateId((int)$userId)])->all();

        if (!$tasks) {
            throw new TaskNotFoundException();
        } elseif (empty($tasks)) {
            throw new TaskNotFoundException('No tasks found for this user.');
        }

        return $tasks;
    }

    public function beforeValidate()
    {
        
        if ($this->isNewRecord) {
            $raw = Yii::$app->request->getBodyParams();
            if (!array_key_exists('status', $raw)) {
                $this->status = self::STATUS_NEW;
            }
        }

        return parent::beforeValidate();
    }

    
    public function beforeSave($insert)
    {
        if ($insert && $this->id === null) {
            $last = static::find()->orderBy(['id' => SORT_DESC])->one();
            $this->id = $last ? $last->id + 1 : 1;
        }

        return parent::beforeSave($insert);
    }
}
