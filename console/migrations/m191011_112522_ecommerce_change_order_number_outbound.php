<?php

use yii\db\Migration;

/**
 * Class m191011_112522_ecommerce_change_order_number_outbound
 */
class m191011_112522_ecommerce_change_order_number_outbound extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `ecommerce_outbound`
CHANGE `order_number` `order_number` varchar(32) COLLATE 'utf8_general_ci' NULL DEFAULT '' COMMENT 'Order number' AFTER `responsible_delivery_id`,
CHANGE `external_order_number` `external_order_number` varchar(32) COLLATE 'utf8_general_ci' NULL AFTER `order_number`;");

    }

    public function down()
    {
        return false;
    }
}