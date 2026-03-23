<?php

use yii\db\Schema;
use yii\db\Migration;

class m140810_123804_add_column_product_name_to_order_process_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order_process}}','product_name',Schema::TYPE_STRING . '(256) NOT NULL COMMENT "Product name" AFTER `product_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%order_process}}','product_name');
    }
}
