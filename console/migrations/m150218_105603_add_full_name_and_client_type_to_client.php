<?php

use yii\db\Schema;
use yii\db\Migration;

class m150218_105603_add_full_name_and_client_type_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('{{%clients}}','full_name', Schema::TYPE_STRING . ' NULL AFTER `title`');
        $this->addColumn('{{%clients}}','client_type', Schema::TYPE_INTEGER . ' NULL AFTER `user_id`');
    }

    public function down()
    {
        echo "m150218_105603_add_full_name_and_client_type_to_client cannot be reverted.\n";

        return false;
    }
}
