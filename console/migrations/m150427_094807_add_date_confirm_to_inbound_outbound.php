<?php

use yii\db\Schema;
use yii\db\Migration;

class m150427_094807_add_date_confirm_to_inbound_outbound extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','date_confirm', Schema::TYPE_INTEGER . '(11) NULL COMMENT "Confirmation timestamp" AFTER `end_datetime`');
        $this->addColumn('{{%inbound_orders}}','date_confirm', Schema::TYPE_INTEGER . '(11) NULL COMMENT "Confirmation timestamp" AFTER `end_datetime`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','date_confirm');
        $this->dropColumn('{{%inbound_orders}}','date_confirm');
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
