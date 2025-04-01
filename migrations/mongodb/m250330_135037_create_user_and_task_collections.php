<?php

class m250330_135037_create_user_and_task_collections extends \yii\mongodb\Migration
{
    public function up()
    {
        // Create 'user' collection
        $this->createCollection('user');

        // Add unique indexes to 'user'
        $this->createIndex('user', ['login' => 1], ['unique' => true]);
        $this->createIndex('user', ['email' => 1], ['unique' => true]);

        // Create 'task' collection
        $this->createCollection('task');

        // Add index on 'status' if needed
        $this->createIndex('task', ['status' => 1]);

        echo "User and Task collections created with indexes.\n";
    }

    public function down()
    {
        $this->dropCollection('user');
        $this->dropCollection('task');
        echo "Collections dropped.\n";

        return true;
    }
}
