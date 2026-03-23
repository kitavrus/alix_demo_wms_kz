<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m241111_162010_add_ecommerce_stock_datamatrix
 */
class m241111_162010_add_ecommerce_stock_datamatrix extends Migration
{
	public function up()
	{
		$this->addColumn('{{%ecommerce_stock}}','inbound_datamatrix_id', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Inbound datamatrix id" AFTER `product_qrcode`');
		$this->addColumn('{{%ecommerce_stock}}','inbound_datamatrix_code', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Inbound datamatrix code" AFTER `inbound_datamatrix_id`');
	}

	public function down()
	{
		$this->dropColumn('{{%ecommerce_stock}}','inbound_datamatrix_id');
		$this->dropColumn('{{%ecommerce_stock}}','inbound_datamatrix_code');
	}
}
