<?php

use yii\db\Schema;
use yii\db\Migration;

class m140810_123756_add_column_product_price_to_order_process_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order_process}}','product_price',Schema::TYPE_DECIMAL . '(26,6) NULL COMMENT "Product price" AFTER `product_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%order_process}}','product_price');
    }
}
