<?php

use yii\db\Schema;
use yii\db\Migration;

class m150810_141737_add_agent_id_and_car_id_to_default_sub_routes extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_default_sub_routes}}', 'agent_id', Schema::TYPE_SMALLINT . '(32) NULL DEFAULT NULL AFTER `client_id`');
        $this->addColumn('{{%tl_delivery_proposal_default_sub_routes}}', 'car_id', Schema::TYPE_SMALLINT . '(32) NULL DEFAULT NULL AFTER `agent_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_default_sub_routes}}', 'agent_id');
        $this->dropColumn('{{%tl_delivery_proposal_default_sub_routes}}', 'car_id');
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