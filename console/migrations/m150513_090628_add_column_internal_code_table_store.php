<?php

use yii\db\Schema;
use yii\db\Migration;

class m150513_090628_add_column_internal_code_table_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','internal_code', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Internal incremental code for Colins Code" AFTER `shop_code2`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','internal_code');
    }
}
