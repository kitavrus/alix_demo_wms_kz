<?php

use yii\db\Schema;
use yii\db\Migration;

class m140804_201215_add_column_price_to_sync_products_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%sync_products}}','price',Schema::TYPE_DECIMAL . '(26,6) NULL COMMENT "Product price" AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%sync_products}}','price');
    }
}