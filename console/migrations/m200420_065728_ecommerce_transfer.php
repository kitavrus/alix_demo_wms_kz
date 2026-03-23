<?php

use yii\db\Migration;

/**
 * Class m200420_065728_ecommerce_transfer
 */
class m200420_065728_ecommerce_transfer extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_transfer', [
            'id' => $this->primaryKey(),

            'client_BatchId' => $this->string(18)->defaultValue('')->comment("Номер партии клиента"),
            'client_Status' => $this->string(36)->defaultValue('')->comment("Статус клиента"),
            'client_LcBarcode' => $this->string(18)->defaultValue('')->comment("Короб клиента"),
            'expected_box_qty' => $this->integer(11)->defaultValue(0)->comment("Expected box qty"),

            'status' => $this->string(36)->defaultValue('')->comment("Статус"),
            'api_status' => $this->string(36)->defaultValue('')->comment("API cтатус"),

            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Expected qty"),
            'allocated_qty' => $this->integer(11)->defaultValue(0)->comment("Allocated qty"),
            'accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted qty"),

            'print_picking_list_date' =>$this->integer(11)->defaultValue(null)->comment("Print picking list date"),
            'begin_datetime' =>$this->integer(11)->defaultValue(null)->comment("Begin scanning datetime"),
            'end_datetime' =>$this->integer(11)->defaultValue(null)->comment("End scanning datetime"),
            'packing_date' =>$this->integer(11)->defaultValue(null)->comment("Packing date"),
            'date_left_warehouse' =>$this->integer(11)->defaultValue(null)->comment("Date left warehouse"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_transfer}}');
    }
}