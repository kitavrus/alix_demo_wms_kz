<?php

use yii\db\Migration;

/**
 * Class m190715_061005_ecommerce_inbound_items
 */
class m190715_061005_ecommerce_inbound_items extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_inbound_items', [
            'id' => $this->primaryKey(),
            'inbound_id' => $this->integer(11)->defaultValue(0)->comment("Inbound id"),
            'product_id' => $this->integer(11)->defaultValue(0)->comment("Product id"),

            'client_box_barcode' => $this->string(18)->defaultValue('')->comment("Короб клиента"),
            'client_inbound_id' => $this->string(18)->defaultValue('')->comment("inbound id client"),
            'client_lot_sku' => $this->string(18)->defaultValue('')->comment("SKU лота клинта"),
            'client_product_sku' => $this->string(18)->defaultValue('')->comment("SKU товара клинта"),

            'our_box_barcode' => $this->string(18)->defaultValue('')->comment("Наш короб"),
            'lot_barcode' => $this->string(18)->defaultValue('')->comment("Шк лота"),
            'product_barcode' => $this->string(18)->defaultValue('')->comment("Шк товара"),
            'product_expected_qty' => $this->integer(11)->defaultValue(0)->comment("Product Expected qty"),
            'product_accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Product Accepted qty"),


//            'product_sku' => $this->string(64)->defaultValue('')->comment("Product sku"),
//            'product_name' => $this->string(64)->defaultValue('')->comment("Product name"),
//            'product_model' => $this->string(64)->defaultValue('')->comment("Product model"),


//            'product_price' => $this->string(11)->defaultValue(0)->comment("Product price"),
//            'client_box_barcode' => $this->string(24)->defaultValue(0)->comment("Box barcode from client"),
//
//            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Expected qty"),
//            'accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted qty"),

//            'expected_box_qty' => $this->integer(11)->defaultValue(0)->comment("Expected box qty"),
//            'accepted_box_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted box qty"),
//
//            'expected_lot_qty' => $this->integer(11)->defaultValue(0)->comment("Expected lot qty"),
//            'accepted_lot_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted lot qty"),
//
//            'expected_product_qty' => $this->integer(11)->defaultValue(0)->comment("Expected product qty"),
//            'accepted_product_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted product qty"),


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
        $this->dropTable('{{%ecommerce_inbound_items}}');
    }
}
