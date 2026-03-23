<?php

use yii\db\Schema;
use yii\db\Migration;

class m150218_125903_add_full_name_to_client_employees extends Migration
{
    public function up()
    {
        $this->addColumn('{{%client_employees}}','full_name', Schema::TYPE_STRING . ' NULL AFTER `password`');
    }

    public function down()
    {
        echo "m150218_125903_add_full_name_to_client_employees cannot be reverted.\n";

        return false;
    }
}
