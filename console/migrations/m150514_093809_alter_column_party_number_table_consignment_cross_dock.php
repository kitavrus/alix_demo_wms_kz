<?php

use yii\db\Schema;
use yii\db\Migration;

class m150514_093809_alter_column_party_number_table_consignment_cross_dock extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `consignment_cross_dock` CHANGE `party_number` `party_number` VARCHAR(32) NULL DEFAULT NULL COMMENT 'Party number, received from the client'");
    }

    public function down()
    {
        echo "m150514_093809_alter_column_party_number_table_consignment_cross_dock cannot be reverted.\n";

        return false;
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
