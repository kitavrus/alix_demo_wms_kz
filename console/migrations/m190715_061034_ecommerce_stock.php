<?php

use yii\db\Migration;

/**
 * Class m190715_061034_ecommerce_stock
 */
class m190715_061034_ecommerce_stock extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_stock', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'warehouse_id' => $this->integer(11)->defaultValue(0)->comment("Warehouse id"),
            'scan_in_employee_id' => $this->integer(11)->defaultValue(0)->comment("Scan inbound employee id"),
            'scan_out_employee_id' => $this->integer(11)->defaultValue(0)->comment("Scan outbound employee id"),
            'inbound_id' => $this->integer(11)->defaultValue(0)->comment("Inbound id"),
            'inbound_item_id' => $this->integer(11)->defaultValue(0)->comment("Inbound item id"),
            'outbound_id' => $this->integer(11)->defaultValue(0)->comment("Outbound id"),
            'outbound_item_id' => $this->integer(11)->defaultValue(0)->comment("Outbound item id"),

            'client_box_barcode' => $this->string(18)->defaultValue(0)->comment("Шк приходного короба клиента"),
            'client_inbound_id' => $this->string(18)->defaultValue('')->comment("inbound id client"),
            'client_lot_sku' => $this->string(18)->defaultValue('')->comment("SKU лота клинта"),
            'client_product_sku' => $this->string(18)->defaultValue('')->comment("SKU товара клинта"),
            'lot_barcode' => $this->string(18)->defaultValue(0)->comment("Шк лота"),
            'product_barcode' => $this->string(64)->defaultValue('')->comment("Шк товара"),

            'box_address_barcode' => $this->string(18)->defaultValue(0)->comment("Адрес короба"),
            'place_address_barcode' => $this->string(18)->defaultValue(0)->comment("Адрес полки"),
            'outbound_box' => $this->string(18)->defaultValue(0)->comment("Шк короба в котором отгружаем"),

            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),
            'status_inbound' => $this->smallInteger()->defaultValue(0)->comment("Status inbound"),
            'status_outbound' => $this->smallInteger()->defaultValue(0)->comment("Status outbound"),
            'status_availability' => $this->smallInteger()->defaultValue(0)->comment("Доступен для резервировани или нет"),

            'condition_type' => $this->smallInteger()->defaultValue(0)->comment("Состояние товара: норм, брак, частичный брак"),

            'product_id' => $this->integer(11)->defaultValue(0)->comment("Product id"),
            'product_sku' => $this->string(64)->defaultValue('')->comment("Product sku"),
            'product_name' => $this->string(64)->defaultValue('')->comment("Product name"),
            'product_model' => $this->string(64)->defaultValue('')->comment("Product model"),


            'product_price' => $this->string(11)->defaultValue(0)->comment("Product price"),


            'scan_out_datetime' => $this->integer(11)->defaultValue(0)->comment("Scan outbound datetime"),
            'scan_in_datetime' => $this->integer(11)->defaultValue(0)->comment("Scan inbound datetime"),
            'scan_reserved_datetime' => $this->integer(11)->defaultValue(0)->comment("Reserved inbound datetime"),
            'address_sort_order' => $this->integer(11)->defaultValue(0)->comment("Address sort order"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_stock}}');
    }
}
