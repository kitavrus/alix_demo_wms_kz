<?php

use yii\db\Schema;
use yii\db\Migration;

class m141017_035305_add_field_password_to_table_clients extends Migration
{
    public function up()
    {
        $this->addColumn('{{%clients}}','password',Schema::TYPE_STRING . '(64) NULL COMMENT "Password" AFTER `username`');
    }

    public function down()
    {
        $this->dropColumn('{{%clients}}','password');
    }
}
