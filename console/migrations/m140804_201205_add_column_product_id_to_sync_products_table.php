<?php

use yii\db\Schema;
use yii\db\Migration;

class m140804_201205_add_column_product_id_to_sync_products_table extends Migration
{
    public function up()
    {
        // TODO  ALTER TABLE `sync_products` CHANGE `product_id` `product_id` INT( 11 ) NULL DEFAULT NULL COMMENT 'Internal product id';
        $this->addColumn('{{%sync_products}}','product_id',Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Internal product id" AFTER `id`');

    }

    public function down()
    {
        $this->dropColumn('{{%sync_products}}','product_id');
    }
}
