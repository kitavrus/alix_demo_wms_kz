<?php

use yii\db\Schema;
use yii\db\Migration;

class m150422_134349_add_barcode_to_store_and_agents extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','internal_barcode', Schema::TYPE_STRING . '(128) NULL COMMENT "Our barcode" AFTER `id`');
        $this->addColumn('{{%tl_agents}}','internal_barcode', Schema::TYPE_STRING . '(128) NULL COMMENT "Our barcode" AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','internal_barcode');
        $this->dropColumn('{{%tl_agents}}','internal_barcode');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
