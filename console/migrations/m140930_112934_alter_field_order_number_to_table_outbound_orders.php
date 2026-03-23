<?php

use yii\db\Schema;
use yii\db\Migration;

class m140930_112934_alter_field_order_number_to_table_outbound_orders extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `outbound_orders` CHANGE `order_number` `order_number` VARCHAR( 64 ) NULL DEFAULT NULL COMMENT 'Order number, received from the client'");
    }

    public function down()
    {
        echo "m140930_112934_alter_field_order_number_to_table_outbound_orders cannot be reverted.\n";

        return false;
    }
}