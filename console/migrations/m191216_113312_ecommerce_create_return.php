<?php

use yii\db\Migration;

/**
 * Class m191216_113312_ecommerce_create_return
 */
class m191216_113312_ecommerce_create_return extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_return', [
            'id' => $this->primaryKey(),
            'outbound_id' => $this->integer(11)->defaultValue(0)->comment("Outbound id"),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),

            'order_number' => $this->string(36)->defaultValue('')->comment("Order number"),
            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Expected product qty"),
            'accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted product qty"),

            'customer_name' => $this->string(256)->defaultValue('')->comment("Customer full name"),
            'city' => $this->string(128)->defaultValue('')->comment("city"),
            'customer_address' => $this->string(512)->defaultValue('')->comment("Адрес"),
            'client_ReferenceNumber' => $this->string(128)->defaultValue('')->comment("Cargo company ReferenceNumber"),

            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),

            'begin_datetime' =>$this->integer(11)->defaultValue(null)->comment("Begin scanning datetime"),
            'end_datetime' =>$this->integer(11)->defaultValue(null)->comment("End scanning datetime"),
            'date_confirm' =>$this->integer(11)->defaultValue(null)->comment("End scanning datetime"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_return}}');
    }
}
