<?php

use yii\db\Schema;
use yii\db\Migration;

class m150420_060533_add_fields_shop_code2_table_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','shop_code2', Schema::TYPE_STRING . '(128) NULL DEFAULT 0 COMMENT "External shop code 2" AFTER `shop_code`');
        $this->alterColumn('{{%store}}','shop_code', Schema::TYPE_STRING . '(128) NULL DEFAULT 0 COMMENT "External shop code 2"');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','shop_code2');
    }
}
