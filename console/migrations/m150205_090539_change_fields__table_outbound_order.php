<?php

use yii\db\Schema;
use yii\db\Migration;

class m150205_090539_change_fields__table_outbound_order extends Migration
{
    public function up()
    {
        $this->renameColumn("outbound_orders",'allocate_qty','allocated_qty');
        $this->renameColumn("outbound_orders",'allocate_number_places_qty','allocated_number_places_qty');

        $this->renameColumn("outbound_order_items",'allocate_qty','allocated_qty');
        $this->renameColumn("outbound_order_items",'allocate_number_places_qty','allocated_number_places_qty');
    }


    public function down()
    {
        $this->renameColumn("outbound_orders",'allocated_qty','allocate_qty');
        $this->renameColumn("outbound_orders",'allocated_number_places_qty','allocate_number_places_qty');

        $this->renameColumn("outbound_order_items",'allocate_qty','allocated_qty');
        $this->renameColumn("outbound_order_items",'allocate_number_places_qty','allocated_number_places_qty');

        return false;
    }
}
