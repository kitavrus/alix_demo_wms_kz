<?php

use yii\db\Migration;

/**
 * Handles the creation for table `movement_pick_lists`.
 */
class m170801_121438_create_table_movement_pick_lists extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('movement_pick_lists', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'employee_id' => $this->integer(11)->defaultValue(0)->comment("Employee id"),
            'order_id' => $this->integer(11)->defaultValue(0)->comment("Movement id"),
            'page_number' => $this->integer(11)->defaultValue(0)->comment("Page number"),
            'page_total' => $this->integer(11)->defaultValue(0)->comment("Page total"),
            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),
            'barcode' => $this->string(128)->defaultValue('')->comment("List barcode"),
            'employee_barcode' => $this->string(32)->defaultValue('')->comment("Barcode: employee"),
            'begin_datetime' => $this->integer(11)->defaultValue(0)->comment("Start time of the picking order"),
            'end_datetime' => $this->integer(11)->defaultValue(0)->comment("End time of the picking order"),

            'created_user_id' =>$this->integer()->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer()->defaultValue(null)->comment("Updated user id"),

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
        $this->dropTable('movement_pick_lists');
    }
}