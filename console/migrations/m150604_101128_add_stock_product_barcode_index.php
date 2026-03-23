<?php

use yii\db\Schema;
use yii\db\Migration;

class m150604_101128_add_stock_product_barcode_index extends Migration
{
    public function up()
    {
        $this->createIndex('product_barcode', 'stock', 'product_barcode');
        $this->createIndex('product_barcode', 'outbound_order_items', 'product_barcode');
        $this->createIndex('product_barcode', 'inbound_order_items', 'product_barcode');
    }

    public function down()
    {
        echo "m150604_101128_add_stock_product_barcode_index cannot be reverted.\n";

        return false;
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
