<?php

use yii\db\Migration;

/**
 * Class m190728_164241_ecommerce_picking_list
 */
class m190728_164241_ecommerce_picking_list extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_picking_list', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'employee_id' => $this->integer(11)->defaultValue(0)->comment("Employee id"),
            'outbound_id' => $this->integer(11)->defaultValue(0)->comment("Outbound  id"),
            'page_number' => $this->integer(11)->defaultValue(0)->comment("Page number"),
            'page_total' => $this->integer(11)->defaultValue(0)->comment("Page total"),
            'status' => $this->integer(11)->defaultValue(0)->comment("Status"),

            'barcode' => $this->string(64)->defaultValue('')->comment("List barcode"),

            'begin_datetime' =>$this->integer(11)->defaultValue(null)->comment("The start time of the picking order"),
            'end_datetime' =>$this->integer(11)->defaultValue(null)->comment("The end time of the picking order"),

            'client_Priority' =>$this->integer(11)->defaultValue(0)->comment("Client Priority"),
            'client_ShippingCountryCode' => $this->string(24)->defaultValue('')->comment("client Shipping Country Code"),
            'client_ShippingCity' => $this->string(64)->defaultValue('')->comment("Client Shipping City"),
            'client_PackMessage' => $this->text()->defaultValue('')->comment("Client Pack Message"),
            'client_GiftWrappingMessage' => $this->text()->defaultValue('')->comment("Client Gift Wrapping Message"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_picking_list}}');
    }
}