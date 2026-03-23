<?php

use yii\db\Schema;
use yii\db\Migration;

class m150312_092135_change_date_field_in_inbound_outbound_audit extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `inbound_orders_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
        $this->execute("ALTER TABLE `inbound_order_items_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
        $this->execute("ALTER TABLE `outbound_orders_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
        $this->execute("ALTER TABLE `outbound_order_items_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
        $this->execute("ALTER TABLE `outbound_picking_lists_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
        $this->execute("ALTER TABLE `stock_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");


    }

    public function down()
    {
        echo "m150312_092135_change_date_field_in_inbound_outbound_audit cannot be reverted.\n";

        return false;
    }
}
