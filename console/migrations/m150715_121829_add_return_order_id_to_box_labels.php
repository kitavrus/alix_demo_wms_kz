<?php

use yii\db\Schema;
use yii\db\Migration;

class m150715_121829_add_return_order_id_to_box_labels extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_box_labels}}','return_order_id',Schema::TYPE_INTEGER . '(11) NULL AFTER `outbound_order_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_box_labels}}','return_order_id');

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
