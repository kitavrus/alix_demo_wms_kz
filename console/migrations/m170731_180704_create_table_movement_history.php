<?php

use yii\db\Migration;

/**
 * Handles the creation for table `movement_history`.
 */
class m170731_180704_create_table_movement_history extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('movement_history', [
            'id' => $this->primaryKey(),
            'client_order_id' => $this->string(128)->defaultValue('')->comment("Client order id"),

            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'stock_id' => $this->integer(11)->defaultValue(0)->comment("Stock id"),
            'inbound_id' => $this->integer(11)->defaultValue(0)->comment("Inbound id"),
            'movement_id' => $this->integer(11)->defaultValue(0)->comment("Movement id"),
            'outbound_id' => $this->integer(11)->defaultValue(0)->comment("Outbound id"),

            'from_zone_id' => $this->integer(11)->defaultValue(0)->comment("From zone id"),
            'to_zone_id' => $this->integer(11)->defaultValue(0)->comment("To zone id"),

            'product_barcode' => $this->string(128)->defaultValue('')->comment("Product barcode"),
            'product_model' => $this->string(128)->defaultValue('')->comment("Product model"),
            'product_sku' => $this->string(128)->defaultValue('')->comment("Product sku"),
            'product_qty' => $this->integer(11)->defaultValue(0)->comment("Product quantity"),

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
        $this->dropTable('movement_history');
    }
}