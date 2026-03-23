<?php

use yii\db\Schema;
use yii\db\Migration;

class m150721_065109_alter_agent_billing_condition extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_agents_billing_conditions` CHANGE `rule_tyle` `rule_type` SMALLINT (32) NULL DEFAULT 0 COMMENT 'rule type'");
    }

    public function down()
    {
        echo "m150721_065109_alter_agent_billing_condition cannot be reverted.\n";

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