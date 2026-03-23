<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_consignment_universal_orders_items`.
 */
class m160618_071001_create_table_consignment_universal_orders_items extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('consignment_universal_orders_items', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer()->defaultValue(0),
            'consignment_universal_id' => $this->integer()->defaultValue(0),
            'consignment_universal_order_id' => $this->integer()->defaultValue(0),

            'from_point_id' => $this->integer()->defaultValue(0)->comment("Internal from point id "),
            'from_point_client_id' => $this->string(128)->defaultValue(0)->comment("Client from point id "),
            'to_point_id' => $this->integer()->defaultValue(0)->comment("Internal from point id "),
            'to_point_client_id' => $this->string(128)->defaultValue(0)->comment("Client from point id "),

            'order_type' => $this->smallInteger()->defaultValue(0)->comment("Order party type: stock, cross-doc, inbound, outbound etc"),
            'order_type_client' => $this->string(128)->defaultValue('')->comment("Order party type from client: stock, cross-doc, inbound, outbound etc"),

            'party_number' => $this->string(128)->comment("Party number, received from the client"),
            'order_number' => $this->string(128)->comment("Order number, received from the client"),

            'box_barcode_client' => $this->string(28)->comment("Box barcode client, received from the client"),
            'box_barcode' => $this->string(28)->comment("Box barcode, received from the client"),

            'product_barcode' => $this->string(28)->comment("Product barcode, received from the client"),
            'product_id' => $this->string(28)->comment("Product id, received from the client"),

            'status' => $this->smallInteger()->defaultValue(0)->comment("Status new, in process, complete, etc"),
            'status_created_on_client' => $this->string(128)->defaultValue(0)->comment("Status created on client side"),

            'expected_qty' => $this->integer()->defaultValue(0)->comment("Expected product quantity in party"),
            'accepted_qty' => $this->integer()->defaultValue(0)->comment("Accepted product quantity in party"),
            'allocated_qty' => $this->integer()->defaultValue(0)->comment("Allocated product quantity in party"),

            'accepted_number_places_qty' => $this->integer()->defaultValue(0)->comment("Accepted number places quantity in party"),
            'expected_number_places_qty' => $this->integer()->defaultValue(0)->comment("Expected number places quantity in party"),
            'allocated_number_places_qty' => $this->integer()->defaultValue(0)->comment("Allocated number places quantity in party"),

            'extra_fields' => $this->text()->defaultValue('')->comment("Example JSON: order_number, who received order, etc ..."),
            'field_extra1' => $this->text()->defaultValue('')->comment("Extra field 1"),
            'field_extra2' => $this->text()->defaultValue('')->comment("Extra field 2"),
            'field_extra3' => $this->text()->defaultValue('')->comment("Extra field 3"),
            'field_extra4' => $this->text()->defaultValue('')->comment("Extra field 4"),
            'field_extra5' => $this->text()->defaultValue('')->comment("Extra field 5"),

            'created_user_id' => $this->integer()->defaultValue(0),
            'updated_user_id' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->defaultValue(0),
            'updated_at' => $this->integer()->defaultValue(0),
            'deleted' => $this->integer()->defaultValue(0),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('consignment_universal_orders_items');
    }
}