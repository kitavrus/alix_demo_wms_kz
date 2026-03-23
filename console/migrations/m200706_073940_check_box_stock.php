<?php

use yii\db\Migration;

/**
 * Class m200706_073940_ecommerce_check_box_stock
 */
class m200706_073940_check_box_stock extends Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('check_box_stock', [
            'id' => $this->primaryKey(),
            'inventory_id' => $this->integer(11)->defaultValue(0)->comment("inventory id"),
            'check_box_id' => $this->integer(11)->defaultValue(0)->comment("check box id"),
            'stock_id' => $this->integer(11)->defaultValue(0)->comment("Product id"),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'warehouse_id' => $this->integer(11)->defaultValue(0)->comment("Warehouse id"),
            'employee_id' => $this->integer(11)->defaultValue(0)->comment("Employee id"),

            'box_barcode' => $this->string(15)->defaultValue('')->comment("Box barcode"),
            'place_address' => $this->string(15)->defaultValue('')->comment("Place address barcode"),

            'stock_inbound_id' => $this->integer(11)->defaultValue(0)->comment("stock inbound id"),
            'stock_inbound_item_id' => $this->integer(11)->defaultValue(0)->comment("stock inbound item id"),

            'stock_outbound_id' => $this->integer(11)->defaultValue(0)->comment("stock outbound id"),
            'stock_outbound_item_id' => $this->integer(11)->defaultValue(0)->comment("stock outbound item id"),
            'stock_status_availability' => $this->integer(11)->defaultValue(0)->comment("stock status availability"),
            'stock_client_product_sku' => $this->string(14)->defaultValue('')->comment("Stock client product sku"),
            'stock_inbound_status' => $this->smallInteger()->defaultValue(0)->comment("Status inbound"),
            'stock_outbound_status' => $this->smallInteger()->defaultValue(0)->comment("Status outbound"),
            'stock_condition_type' => $this->smallInteger()->defaultValue(0)->comment("Condition type"),

            'product_barcode' => $this->string(14)->defaultValue('')->comment("Product Barcode"),

            'serialized_data_stock' => $this->text()->defaultValue('')->comment("Serialized data stock"),

            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),


            'scanned_datetime' =>$this->integer(11)->defaultValue(null)->comment("Scanned datetime"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

        $this->execute("ALTER TABLE `check_box_stock`
ADD INDEX `check_box_id` (`check_box_id`),
ADD INDEX `inventory_id` (`inventory_id`),
ADD INDEX `box_barcode` (`box_barcode`),
ADD INDEX `place_address` (`place_address`),
ADD INDEX `product_barcode` (`product_barcode`),
ADD INDEX `status` (`status`);");
    }

    public function safeDown()
    {
        $this->dropTable('{{%check_box_stock}}');
    }
}
