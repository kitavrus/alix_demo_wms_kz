<?php

use yii\db\Migration;

/**
 * Handles the creation for table `movements`.
 */
class m170719_191824_create_movements_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('movements', [
            'id' => $this->primaryKey(),
            'client_order_id' => $this->string(128)->defaultValue("")->comment("Client order number id"),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),

            'order_number' => $this->string(128)->defaultValue("")->comment("Order number"),
            'parent_order_number' => $this->string(128)->defaultValue("")->comment("Parent order number"),
            'status' =>$this->smallInteger()->defaultValue(0)->comment("Status"),
            'comments' =>$this->text()->defaultValue("")->comment("Comments"),
            'extra_fields' =>$this->text()->defaultValue("")->comment("Extra fields"),

            'expected_qty' =>$this->integer(11)->defaultValue(0)->comment("Expected qty"),
            'accepted_qty' =>$this->integer(11)->defaultValue(0)->comment("Accepted qty"),
            'allocated_qty' =>$this->integer(11)->defaultValue(0)->comment("Allocated qty"),

            'from_zone' =>$this->smallInteger()->defaultValue(null)->comment("From our zone"),
            'to_zone' =>$this->smallInteger()->defaultValue(null)->comment("To our zone"),

            'client_datetime' =>$this->string(128)->defaultValue("")->comment("Client datetime"),

            'field_extra1' =>$this->string(64)->defaultValue('')->comment("Extra field 1"),
            'field_extra2' =>$this->string(128)->defaultValue('')->comment("Extra field 2"),
            'field_extra3' =>$this->string(256)->defaultValue('')->comment("Extra field 3"),
            'field_extra4' =>$this->text()->defaultValue('')->comment("Extra field 4"),
            'field_extra5' =>$this->text()->defaultValue('')->comment("Extra field 5"),

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
        $this->dropTable('movements');
    }
}