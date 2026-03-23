<?php

use yii\db\Schema;
use yii\db\Migration;

class m140825_063314_change_column_number_places_to_transport_logistics_order_table extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `transport_logistics_order` CHANGE `number_places` `number_places` INT( 11 ) NULL COMMENT 'Number of places'");
    }

    public function down()
    {
        echo "m140825_062544_change_column_mc_to_transport_logistics_order_table cannot be reverted.\n";

        return false;
    }
}
