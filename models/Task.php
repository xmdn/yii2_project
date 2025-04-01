<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class Task extends ActiveRecord
{
    const STATUS_NEW = 'New';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_DONE = 'Done';

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
            ['status', 'default', 'value' => self::STATUS_NEW],
        ];
    }

    public static function querySearch($query, $search)
    {
        return $query->andFilterWhere(['or',
            ['like', 'title', $search],
            ['like', 'status', $search],
        ]);
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
