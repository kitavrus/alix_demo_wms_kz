<?php

use yii\db\Schema;
use yii\db\Migration;

class m150722_062825_add_transport_type_to_billing_cond extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_agents_billing_conditions}}','transport_type',Schema::TYPE_SMALLINT . '(32) NULL DEFAULT 0 AFTER `rule_type`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_agents_billing_conditions}}','transport_type`');
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