<?php

use yii\db\Migration;

/**
 * Class m240331_150626_add_column_product_barcode_to_change_address_place
 */
class m240331_150626_add_column_product_barcode_to_change_address_place extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addColumn('{{%change_address_place}}', 'product_barcode', $this->string(32)->defaultValue('')->comment("шк товара")->after('to_barcode'));
		$this->addColumn('{{%change_address_place}}', 'product_qty', $this->integer(11)->defaultValue(0)->comment("кол-во товара")->after('product_barcode'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
	}
}
