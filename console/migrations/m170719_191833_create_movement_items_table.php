<?php

use yii\db\Migration;

/**
 * Handles the creation for table `movement_items`.
 */
class m170719_191833_create_movement_items_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('movement_items', [
            'id' => $this->primaryKey(),
            'movement_id' => $this->integer(11)->defaultValue(0)->comment("Movement id"),

            'product_id' => $this->integer(11)->defaultValue(0)->comment("Product id"),
            'product_name' => $this->string(128)->defaultValue("")->comment("Product name"),
            'product_model' => $this->string(128)->defaultValue("")->comment("Product model"),
            'product_sku' => $this->string(128)->defaultValue("")->comment("Product sku"),
            'product_description' => $this->text()->defaultValue("")->comment("Product description"),
            'product_barcode' => $this->string(128)->defaultValue("")->comment("Product barcode"),
            'product_serialize_data' => $this->text()->defaultValue("")->comment("Client Product data"),

            'status' =>$this->smallInteger()->defaultValue(0)->comment("Status"),
            'expected_qty' =>$this->smallInteger()->defaultValue(0)->comment("Expected qty"),
            'accepted_qty' =>$this->smallInteger()->defaultValue(0)->comment("Accepted qty"),
            'allocated_qty' =>$this->integer(11)->defaultValue(0)->comment("Allocated qty"),

            'comments' =>$this->text()->defaultValue("")->comment("comments"),

            'from_zone' =>$this->smallInteger()->defaultValue(null)->comment("From zone"),
            'to_zone' =>$this->smallInteger()->defaultValue(null)->comment("To zone"),

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
        $this->dropTable('movement_items');
    }
}