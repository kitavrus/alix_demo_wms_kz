<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_placement_unit`.
 */
class m171001_095128_create_table_placement_unit extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('placement_unit', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'zone_id' => $this->integer(11)->defaultValue(0)->comment("Zone id"),
            'count_unit' => $this->integer(11)->defaultValue(0)->comment("Count unit"),
            'type_inout' => $this->smallInteger()->defaultValue(0)->comment("Type inbound or outbound, mix"),
            'barcode' => $this->string(23)->defaultValue('')->comment("Placement unit barcode"),

            'status' => $this->smallInteger()->defaultValue(1)->comment("Status: free, work, close"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),

            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),

            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);

        $this->createTable('placement_unit_flow', [
            'id' => $this->primaryKey(),
            'count_unit' => $this->integer(11)->defaultValue(0)->comment("Count unit"),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("client id"),
            'stock_id' => $this->integer(11)->defaultValue(0)->comment("Stock id"),
            'zone_id' => $this->integer(11)->defaultValue(0)->comment("Zone id"),

            'inbound_order_id' => $this->integer(11)->defaultValue(0)->comment("Inbound order id"),
            'inbound_order_item_id' => $this->integer(11)->defaultValue(0)->comment("Inbound order item id"),

            'outbound_order_id' => $this->integer(11)->defaultValue(0)->comment("Outbound order id"),
            'outbound_order_item_id' => $this->integer(11)->defaultValue(0)->comment("Outbound order item id"),

            'placement_unit_barcode_id' => $this->integer(11)->defaultValue(0)->comment("Placement unit id"),
            'placement_unit_barcode' => $this->string(23)->defaultValue('')->comment("Placement unit barcode"),

            'product_id' => $this->integer(11)->defaultValue(0)->comment("Product id"),
            'product_barcode' => $this->string(23)->defaultValue('')->comment("Product barcode"),
            'product_model' => $this->string(64)->defaultValue('')->comment("Product model"),
            'product_name' => $this->string(256)->defaultValue('')->comment("Product name"),
            'product_sku' => $this->string(64)->defaultValue('')->comment("Product sku"),
            'product_qty' => $this->integer(11)->defaultValue(0)->comment("Product quantity"),

            'status' => $this->smallInteger()->defaultValue(1)->comment("Status: free, work, close"),

            'to_rack_address' => $this->string(23)->defaultValue('')->comment("To rack address barcode"),
            'to_pallet_address' => $this->string(23)->defaultValue('')->comment("To pallet address barcode"),
            'to_box_address' => $this->string(23)->defaultValue('')->comment("To box address barcode"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),

            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),

            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('placement_unit');
        $this->dropTable('placement_unit_flow');
    }
}
