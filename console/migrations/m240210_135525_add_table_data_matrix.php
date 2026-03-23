<?php

use yii\db\Migration;

/**
 * Class m240210_135525_add_table_data_matrix
 */
class m240210_135525_add_table_data_matrix extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addColumn('{{%inbound_data_matrix}}', 'print_status', $this->string()->defaultValue('no')->comment("распечатали или нет")->after('status'));
//		$this->createTable('{{%product_datamatrix}}', [
//			'id' => $this->primaryKey(),
//			'client_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('Клиента'),
//			'inbound_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('ID прихода'),
//			'inbound_item_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('ID строки прихода'),
//			'product_barcode' => $this->string(18)->notNull()->defaultValue('')->comment(''),
//			'product_datamatrix' => $this->text()->notNull()->defaultValue('')->comment(''),
//			'status' => $this->string()->notNull()->defaultValue('')->comment(''),
//
//			'created_user_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('Кто создал'),
//			'modified_user_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('Кто изменил'),
//
//			'created_datetime' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP()')->comment('Когда создали'),
//			'modified_datetime' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()')->comment('Когда изменили'),
//
//		],'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
//		$this->dropTable('{{%product_datamatrix}}');
	}
}