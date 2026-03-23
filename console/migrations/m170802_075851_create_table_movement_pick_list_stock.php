<?php

use yii\db\Migration;

/**
 * Handles the creation for table `movement_pick_list_stock`.
 */
class m170802_075851_create_table_movement_pick_list_stock extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('movement_pick_list_stock', [
            'id' => $this->primaryKey(),
            'movement_id' => $this->integer(11)->defaultValue(0)->comment("Movement id"),
            'movement_pick_id' => $this->integer(11)->defaultValue(0)->comment("Movement pick id"),

            'product_name' => $this->string(128)->defaultValue(0)->comment("Product name"),
            'product_barcode' => $this->string(64)->defaultValue(0)->comment("Product barcode"),
            'product_model' => $this->string(64)->defaultValue(0)->comment("Product model"),
            'product_sku' => $this->string(64)->defaultValue(0)->comment("Product sku"),

            'stock_id' => $this->integer(11)->defaultValue(0)->comment("Stock id"),

            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),

            'from_box' => $this->string(64)->defaultValue('')->comment("From Box"),
            'to_box' => $this->string(64)->defaultValue('')->comment("To Box"),

            'from_address' => $this->string(64)->defaultValue('')->comment("From address"),
            'to_address' => $this->string(64)->defaultValue('')->comment("To address"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),

            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),

            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('movement_pick_list_stock');
    }
}