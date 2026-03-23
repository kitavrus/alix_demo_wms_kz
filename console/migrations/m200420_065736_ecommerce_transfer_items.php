<?php

use yii\db\Migration;

/**
 * Class m200420_065736_ecommerce_transfer_items
 */
class m200420_065736_ecommerce_transfer_items extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_transfer_items', [
            'id' => $this->primaryKey(),
            'transfer_id' => $this->integer()->defaultValue(0)->comment("Transfer id"),
            'client_BatchId' => $this->string(18)->defaultValue('')->comment("Номер партии клиента"),
            'client_OutboundId' => $this->string(18)->defaultValue('')->comment("Номер отгрузки клиента"),
            'client_SkuId' => $this->string(18)->defaultValue('')->comment("SKU ID клиента"),
            'client_Quantity' => $this->integer()->defaultValue(0)->comment("Кол-во товаров клиента"),
            'client_Status' => $this->string(36)->defaultValue('')->comment("Статус клиента"),

            'status' => $this->string(36)->defaultValue('')->comment("Статус"),
            'api_status' => $this->string(36)->defaultValue('')->comment("API cтатус"),

            'product_sku' => $this->string(64)->defaultValue('')->comment("Product sku"),
            'product_name' => $this->string(64)->defaultValue('')->comment("Product name"),
            'product_model' => $this->string(64)->defaultValue('')->comment("Product model"),
            'product_barcode' => $this->string(18)->defaultValue('')->comment("Product Barcode"),

            'begin_datetime' => $this->integer(11)->defaultValue(0)->comment("Begin datetime"),
            'end_datetime' => $this->integer(11)->defaultValue(0)->comment("End datetime"),

            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Expected qty"),
            'allocated_qty' => $this->integer(11)->defaultValue(0)->comment("Allocated qty"),
            'accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted qty"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_transfer_items}}');
    }
}