<?php

use yii\db\Schema;
use yii\db\Migration;

class m150415_063005_add_fields_secondary_recipient_phone extends Migration
{
    public function up()
    {
        $this->addColumn('{{%transportation_order_lead}}','recipient_name_2', Schema::TYPE_STRING . ' NULL AFTER `recipient_name`');
        $this->addColumn('{{%transportation_order_lead}}','recipient_phone_2', Schema::TYPE_STRING . ' NULL AFTER `recipient_phone`');
    }

    public function down()
    {
        $this->dropColumn('{{%transportation_order_lead}}','recipient_name_2');
        $this->dropColumn('{{%transportation_order_lead}}','recipient_phone_2');
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
