<?php

use yii\db\Schema;
use yii\db\Migration;

class m140909_124758_rename_table_transport_logistics_order_to_tl_order_table extends Migration
{
    public function up()
    {
        $this->execute("RENAME TABLE `transport_logistics_order` TO `tl_order`;");
    }

    public function down()
    {
        echo "m140909_124758_rename_table_transport_logistics_order_to_tl_order_table cannot be reverted.\n";

        return false;
    }
}
