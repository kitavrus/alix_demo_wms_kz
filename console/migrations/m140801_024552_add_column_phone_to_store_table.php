<?php

use yii\db\Schema;
use yii\db\Migration;

class m140801_024552_add_column_phone_to_store_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','phone',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Store phone" AFTER `email`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','phone');
    }
}
