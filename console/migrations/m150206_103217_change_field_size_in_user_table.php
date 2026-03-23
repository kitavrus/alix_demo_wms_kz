<?php

use yii\db\Schema;
use yii\db\Migration;

class m150206_103217_change_field_size_in_user_table extends Migration
{
    public function up()
    {
        $this->alterColumn('user', 'username', Schema::TYPE_STRING .'(64)');
    }

    public function down()
    {
        echo "m150206_103217_change_field_size_in_user_table cannot be reverted.\n";

        return false;
    }
}
