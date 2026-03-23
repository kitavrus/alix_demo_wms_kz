<?php

use yii\db\Schema;
use yii\db\Migration;

class m141017_052327_add_field_password_to_table_client_managers extends Migration
{
    public function up()
    {
        $this->addColumn('{{%client_managers}}','password',Schema::TYPE_STRING . '(64) NULL COMMENT "Password" AFTER `name`');
    }

    public function down()
    {
        $this->dropColumn('{{%client_managers}}','password');
    }
}
