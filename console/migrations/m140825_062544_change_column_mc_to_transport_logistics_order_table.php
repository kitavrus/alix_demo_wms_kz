<?php

use yii\db\Schema;
use yii\db\Migration;

class m140825_062544_change_column_mc_to_transport_logistics_order_table extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `transport_logistics_order` CHANGE `mc` `mc` DECIMAL( 26, 3 ) NULL DEFAULT '0.000' COMMENT 'Meters cubic'");
    }

    public function down()
    {
        echo "m140825_062544_change_column_mc_to_transport_logistics_order_table cannot be reverted.\n";

        return false;
    }
}
