<?php

use yii\db\Schema;
use yii\db\Migration;

class m140826_031756_change_and_rename_to_transport_logistics_order_table extends Migration
{
    public function up()
    {
        $this->execute("
        ALTER TABLE `transport_logistics_order` CHANGE `dc` `dc` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `hangers` `hangers` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `other` `other` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `angar` `angar` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `total_qty` `total_qty` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `price_square_meters` `price_square_meters` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `price_total` `price_total` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `costs_region` `costs_region` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `cash_no` `cash_no` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `sale_for_client` `sale_for_client` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `our_profit'` `our_profit'` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `costs_cache'` `costs_cache'` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `transport_logistics_order` CHANGE `with_vat'` `with_vat'` INT( 11 ) NULL DEFAULT '0';

        ");
    }

    public function down()
    {
        echo "m140825_062544_change_column_mc_to_transport_logistics_order_table cannot be reverted.\n";

        return false;
    }
}
