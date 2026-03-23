<?php

use yii\db\Migration;

/**
 * Class m191031_111358_ecommerce_add_index_stock
 */
class m191031_111358_ecommerce_add_index_stock extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `ecommerce_stock` ADD INDEX `client_id` (`client_id`), ADD INDEX `inbound_id` (`inbound_id`), ADD INDEX `client_box_barcode` (`client_box_barcode`), ADD INDEX `product_barcode` (`product_barcode`), ADD INDEX `status_inbound` (`status_inbound`), ADD INDEX `status_outbound` (`status_outbound`), ADD INDEX `status_availability` (`status_availability`), ADD INDEX `box_address_barcode` (`box_address_barcode`), ADD INDEX `place_address_barcode` (`place_address_barcode`);");
    }

    public function down()
    {
        return false;
    }
}