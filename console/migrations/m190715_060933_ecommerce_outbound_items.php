<?php

use yii\db\Migration;

/**
 * Class m190715_060933_ecommerce_outbound_items
 */
class m190715_060933_ecommerce_outbound_items extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_outbound_items', [
            'id' => $this->primaryKey(),
            'outbound_id' => $this->integer(11)->defaultValue(0)->comment("Outbound id"),
            'product_id' => $this->integer(11)->defaultValue(0)->comment("Product id"),

            'product_sku' => $this->string(64)->defaultValue('')->comment("Product sku"),
            'product_name' => $this->string(64)->defaultValue('')->comment("Product name"),
            'product_model' => $this->string(64)->defaultValue('')->comment("Product model"),
            'product_barcode' => $this->string(18)->defaultValue('')->comment("Product Barcode"),

            'begin_datetime' => $this->integer(11)->defaultValue(0)->comment("Begin datetime"),
            'end_datetime' => $this->integer(11)->defaultValue(0)->comment("End datetime"),

            'product_price' => $this->integer(11)->defaultValue(0)->comment("Product price"),

            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Expected qty"),
            'allocated_qty' => $this->integer(11)->defaultValue(0)->comment("Allocated qty"),
            'accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted qty"),

            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_outbound_items}}');
    }
}
