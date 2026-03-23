<?php

use yii\db\Migration;

/**
 * Class m190715_060943_ecommerce_inbound
 */
class m190715_060943_ecommerce_inbound extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_inbound', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),

            'party_number' => $this->string(36)->defaultValue('')->comment("Party number"),
            'order_number' => $this->string(36)->defaultValue('')->comment("Order number"),

            'expected_box_qty' => $this->integer(11)->defaultValue(0)->comment("Expected box qty"),
            'accepted_box_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted box qty"),

            'expected_lot_qty' => $this->integer(11)->defaultValue(0)->comment("Expected lot qty"),
            'accepted_lot_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted lot qty"),

            'expected_product_qty' => $this->integer(11)->defaultValue(0)->comment("Expected product qty"),
            'accepted_product_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted product qty"),

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
        $this->dropTable('{{%ecommerce_inbound}}');
    }
}
