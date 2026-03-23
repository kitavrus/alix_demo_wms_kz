<?php

use yii\db\Migration;

/**
 * Class m240108_152259_add_table_inbound_data_matrix
 */
class m240108_152259_add_table_inbound_data_matrix extends Migration
{


	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('inbound_data_matrix', [
			'id' => $this->primaryKey(),
			'inbound_id' => $this->string(36)->defaultValue('')->comment("ИД приходной накладной"),
			'inbound_item_id' => $this->string(36)->defaultValue('')->comment("ИД строки в приходной накладной"),
			'product_barcode' => $this->string(36)->defaultValue('')->comment("Шк товара"),
			'product_model' => $this->string(36)->defaultValue('')->comment("Модель товара"),
			'data_matrix_code' => $this->text()->defaultValue('')->comment("код дата матрицы"),
			'status' => $this->string(256)->defaultValue('not-scanned')->comment("scanned"),
			'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
			'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
			'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
			'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
			'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
		],$tableOptions);

	}

	public function down()
	{
		$this->dropTable('{{%inbound_data_matrix}}');
	}
}
