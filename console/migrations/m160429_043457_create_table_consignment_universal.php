<?php

use yii\db\Migration;

class m160429_043457_create_table_consignment_universal extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('consignment_universal', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer()->comment("Client store id"),
            'from_point_id' => $this->integer()->defaultValue(0)->comment("Internal from point id "),
            'from_point_client_id' => $this->string(128)->defaultValue(0)->comment("Client from point id "),
            'to_point_id' => $this->integer()->defaultValue(0)->comment("Internal from point id "),
            'to_point_client_id' => $this->string(128)->defaultValue(0)->comment("Client from point id "),

            'party_number' => $this->string(128)->comment("Party number, received from the client"),
            'order_type' => $this->smallInteger()->defaultValue(0)->comment("Order party type: stock, cross-doc, inbound, outbound etc"),
            'status' => $this->smallInteger()->defaultValue(0)->comment("Status new, in process, complete, etc"),
            'status_created_on_client' => $this->string(128)->defaultValue(0)->comment("Status created on client side"),
            'expected_qty' => $this->integer()->defaultValue(0)->comment("Expected product quantity in party"),
            'accepted_qty' => $this->integer()->defaultValue(0)->comment("Accepted product quantity in party"),
            'allocated_qty' => $this->integer()->defaultValue(0)->comment("Allocated product quantity in party"),
            'accepted_number_places_qty' => $this->integer()->defaultValue(0)->comment("Accepted number places quantity in party"),
            'expected_number_places_qty' => $this->integer()->defaultValue(0)->comment("Expected number places quantity in party"),
            'allocated_number_places_qty' => $this->integer()->defaultValue(0)->comment("Allocated number places quantity in party"),
            'extra_fields' => $this->text()->defaultValue('')->comment("Example JSON: order_number, who received order, etc ..."),
            'comment_created_on_client' => $this->text()->defaultValue('')->comment("Comment created on client side"),
            'comment_internal' => $this->text()->defaultValue('')->comment("Comment for internal using"),

            'expected_datetime' => $this->integer()->defaultValue(0)->comment("The expected date of delivery in stock"),
            'data_created_on_client' => $this->integer()->defaultValue(0)->comment("Date time created order on client system"),
            'begin_datetime' => $this->integer()->defaultValue(0)->comment("The start time of the scan party"),
            'end_datetime' => $this->integer()->defaultValue(0)->comment("The end time of the scan party"),
            'created_user_id' => $this->integer()->defaultValue(0),
            'updated_user_id' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->defaultValue(0),
            'updated_at' => $this->integer()->defaultValue(0),
            'deleted' => $this->integer()->defaultValue(0),
        ],$tableOptions);
    }

    public function down()
    {
        $this->dropTable('consignment_universal');
    }
}
