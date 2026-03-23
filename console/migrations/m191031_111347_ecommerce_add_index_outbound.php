<?php

use yii\db\Migration;

/**
 * Class m191031_111347_ecommerce_add_index_outbound
 */
class m191031_111347_ecommerce_add_index_outbound extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `ecommerce_outbound` ADD INDEX `client_id` (`client_id`), ADD INDEX `order_number` (`order_number`), ADD INDEX `status` (`status`);");
        $this->execute("ALTER TABLE `ecommerce_outbound_items` ADD INDEX `outbound_id` (`outbound_id`), ADD INDEX `product_sku` (`product_sku`), ADD INDEX `product_barcode` (`product_barcode`), ADD INDEX `status` (`status`);");
    }

    public function down()
    {
        return false;
    }
}