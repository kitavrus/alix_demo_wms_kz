<?php

use yii\db\Migration;

/**
 * Handles the creation for table `index_for_table_products`.
 */
class m170726_074234_create_index_for_table_products extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `product`
ADD INDEX `client_id` (`client_id`),
ADD INDEX `client_product_id` (`client_product_id`),
ADD INDEX `barcode` (`barcode`),
ADD INDEX `field_extra1` (`field_extra1`);");
    }

    public function down()
    {
        echo "m170726_074234_create_index_for_table_products cannot be reverted.\n";

        return false;
    }
}