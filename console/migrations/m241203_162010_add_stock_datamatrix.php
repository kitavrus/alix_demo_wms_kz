<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m241203_162010_add_stock_datamatrix
 */
class m241203_162010_add_stock_datamatrix extends Migration
{
	public function up()
	{
		$this->addColumn('{{%stock}}','inbound_datamatrix_id', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Inbound datamatrix id" AFTER `product_qrcode`');
		$this->addColumn('{{%stock}}','inbound_datamatrix_code', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Inbound datamatrix code" AFTER `inbound_datamatrix_id`');
	}

	public function down()
	{
		$this->dropColumn('{{%stock}}','inbound_datamatrix_id');
		$this->dropColumn('{{%stock}}','inbound_datamatrix_code');
	}
}
