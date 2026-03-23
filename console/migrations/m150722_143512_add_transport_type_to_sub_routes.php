<?php

use yii\db\Schema;
use yii\db\Migration;

class m150722_143512_add_transport_type_to_sub_routes extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_default_sub_routes}}','transport_type',Schema::TYPE_SMALLINT . '(32) NULL DEFAULT 0 AFTER `client_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_default_sub_routes}}','transport_type');
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