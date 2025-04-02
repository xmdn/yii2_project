<?php

namespace app\components;

class TaskNotFoundException extends \yii\web\NotFoundHttpException
{
    public function __construct($message = 'Task not found', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function forUnprocessed(): self
    {
        return new self('Unprocessed task not found');
    }
}