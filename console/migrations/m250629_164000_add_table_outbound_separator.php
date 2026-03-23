<?php

use stockDepartment\modules\intermode\controllers\outboundSeparator\domain\entities\OutboundSeparator;
use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m250629_164000_add_table_outbound_separator
 */
class m250629_164000_add_table_outbound_separator extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
		$this->createTable('outbound_separator', [
			'id' => $this->primaryKey(),
			'order_number' => $this->string(256)->defaultValue(0)->comment("Order number"),
			'comments' => $this->string(1024)->defaultValue(0)->comment("Comments"),
			'status' => $this->string(256)->defaultValue('')->comment("new,scanned,done"),
			'path_to_file' => $this->text()->defaultValue('')->comment("Путь к файлу"),

			'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
			'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
			'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
			'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
			'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
		]);


        $this->createTable('outbound_separator_items', [
            'id' => $this->primaryKey(),
            'outbound_separator_id' => $this->integer()->defaultValue(0)->comment("OutboundSeparator id"),
            'outbound_id' => $this->integer()->defaultValue(0)->comment("Outbound id"),
            'order_number' => $this->string(256)->defaultValue(0)->comment("Order number"),
            'outbound_box_barcode' => $this->string(256)->defaultValue('')->comment(""),
            'product_barcode' => $this->string(256)->defaultValue('')->comment(""),
            'status' => $this->string(256)->defaultValue('')->comment("new,scanned"),

			'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);


		$this->createTable('outbound_separator_stock', [
			'id' => $this->primaryKey(),
			'outbound_separator_id' => $this->integer()->defaultValue(0)->comment("OutboundSeparator id"),
			'stock_id' => $this->integer()->defaultValue(0)->comment("stock id"),
			'outbound_id' => $this->integer()->defaultValue(0)->comment("Outbound id"),
			'order_number' => $this->string(256)->defaultValue(0)->comment("Order number"),
			'outbound_box_barcode' => $this->string(256)->defaultValue('')->comment(""),
			'product_id' => $this->integer()->defaultValue(0)->comment("Product id"),
			'product_sku' => $this->string(256)->defaultValue("")->comment("Product sku"),
			'product_barcode' => $this->string(256)->defaultValue('')->comment("Product barcode"),
			'status' => $this->string(256)->defaultValue('')->comment("new,scanned"),
			'status_to_out' => $this->string(256)->defaultValue('')->comment("Не отгружать"),
			'stock_data' => $this->text()->defaultValue('')->comment("JSON stock data"),

			'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
			'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
			'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
			'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
			'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
		]);

		$this->addColumn('{{%stock}}','outbound_separator_stock', Schema::TYPE_STRING . ' DEFAULT "_no" COMMENT "" AFTER `field_extra5`');
	}

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('outbound_separator');
        $this->dropTable('outbound_separator_items');
        $this->dropTable('outbound_separator_stock');
		$this->dropColumn('{{%stock}}','outbound_separator_stock');
    }
}
