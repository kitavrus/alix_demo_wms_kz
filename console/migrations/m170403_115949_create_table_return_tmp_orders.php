<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_return_tmp_orders`.
 */
class m170403_115949_create_table_return_tmp_orders extends Migration
{
//    public function init()
//    {
//        $this->db = 'dbDefactoSpecial';
//        parent::init();
//    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('return_tmp_orders', [
            'id' => $this->primaryKey(),
            'client_id' =>$this->integer()->defaultValue(0)->comment("Client"), //

            'from_point_id' => $this->integer()->defaultValue(0)->comment("Internal from point id "),
            'from_point_client_id' => $this->string(128)->defaultValue(0)->comment("Client from point id "),
            'to_point_id' => $this->integer()->defaultValue(0)->comment("Internal from point id "),
            'to_point_client_id' => $this->string(128)->defaultValue(0)->comment("Client from point id "),

            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),

            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Expected qty"),
            'accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted qty"),

            'ttn' => $this->string(128)->defaultValue('')->comment("Ttn number"),
            'party_number' => $this->string(128)->defaultValue('')->comment("Party number"),
            'order_number' => $this->string(128)->defaultValue('')->comment("Order number"),

            'our_box_inbound_barcode' => $this->string(16)->defaultValue('')->comment("Our box inbound barcode"),
            'our_box_to_stock_barcode' => $this->string(16)->defaultValue('')->comment("Our box to stock barcode"),
            'client_box_barcode' => $this->string(16)->defaultValue('')->comment("Client box barcode"),

            'primary_address' => $this->string(28)->defaultValue('')->comment("Primary address"),
            'secondary_address' => $this->string(28)->defaultValue('')->comment("Secondary address"),

            'created_user_id' =>$this->integer()->defaultValue(0)->comment("Created user id"),
            'updated_user_id' =>$this->integer()->defaultValue(0)->comment("Updated user id"),

            'created_at' =>$this->integer()->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer()->defaultValue(null)->comment("Updated at"),

            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('return_tmp_orders');
    }
}
