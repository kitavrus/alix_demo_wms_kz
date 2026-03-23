<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_inbound_unit_address`.
 */
class m171001_122451_create_table_inbound_unit_address extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('inbound_unit_address', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'warehouse_id' => $this->smallInteger()->defaultValue(0)->comment("Warehouse id"),
            'zone_id' => $this->integer(11)->defaultValue(0)->comment("Zone id"),
            'inbound_order_id' => $this->integer(11)->defaultValue(0)->comment("Inbound order id"),
            'code_book_id' => $this->integer(11)->defaultValue(0)->comment("Code book id"),
            'to_rack_address' => $this->string(23)->defaultValue('')->comment("To rack address barcode"),
            'to_pallet_address' => $this->string(23)->defaultValue('')->comment("To pallet address barcode"),
            'to_box_address' => $this->string(23)->defaultValue('')->comment("To box address barcode"),

            'transfer_rack_address' => $this->string(23)->defaultValue('')->comment("Transfer rack address barcode"),
            'transfer_pallet_address' => $this->string(23)->defaultValue('')->comment("Transfer pallet address barcode"),
            'transfer_box_address' => $this->string(23)->defaultValue('')->comment("Transfer box address barcode"),

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
        $this->dropTable('inbound_unit_address');
    }
}
