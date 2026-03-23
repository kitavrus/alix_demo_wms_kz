<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240610_182614_add_product_qrcode_stock
 */
class m240610_182615_add_product_qrcode_stock extends Migration
{
	public function up()
	{
		$this->addColumn('{{%stock}}','product_qrcode', Schema::TYPE_TEXT . ' DEFAULT "" COMMENT "qr code" AFTER `product_barcode`');
	}

	public function down()
	{
		$this->dropColumn('{{%stock}}','product_qrcode');

	}
}
