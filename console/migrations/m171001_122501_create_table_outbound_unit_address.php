<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_outbound_unit_address`.
 */
class m171001_122501_create_table_outbound_unit_address extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('outbound_unit_address', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'warehouse_id' => $this->smallInteger()->defaultValue(0)->comment("Warehouse id"),
            'zone_id' => $this->integer(11)->defaultValue(0)->comment("Zone id"),
            'outbound_order_id' => $this->integer(11)->defaultValue(0)->comment("Outbound order id"),

            'code_book_id' => $this->integer(11)->defaultValue(0)->comment("Code book id"),

            'from_rack_address' => $this->string(23)->defaultValue('')->comment("From rack address barcode"),
            'from_pallet_address' => $this->string(23)->defaultValue('')->comment("From pallet address barcode"),
            'from_box_address' => $this->string(23)->defaultValue('')->comment("From box address barcode"),

            'our_barcode' => $this->string(23)->defaultValue('')->comment("Our Unit barcode"),
            'client_barcode' => $this->string(23)->defaultValue('')->comment("Client Unit barcode"),

            'status' => $this->smallInteger()->defaultValue(1)->comment("Status:"),

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
        $this->dropTable('outbound_unit_address');
    }
}
