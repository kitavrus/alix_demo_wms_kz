<?php

use yii\db\Schema;
use yii\db\Migration;

class m140810_123813_add_column_product_sku_to_order_process_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order_process}}','product_sku',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Product sku" AFTER `product_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%order_process}}','product_sku');
    }
}
