<?php

use yii\db\Schema;
use yii\db\Migration;

class m140801_024600_add_column_phone_mobile_to_store_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','phone_mobile',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Store phone mobile" AFTER `phone`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','phone_mobile');
    }
}
