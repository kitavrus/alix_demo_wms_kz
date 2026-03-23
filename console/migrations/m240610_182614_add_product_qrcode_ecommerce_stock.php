<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240610_182614_add_product_qrcode_ecommerce_stock
 */
class m240610_182614_add_product_qrcode_ecommerce_stock extends Migration
{
	public function up()
	{
		$this->addColumn('{{%ecommerce_stock}}','product_qrcode', Schema::TYPE_TEXT . ' DEFAULT "" COMMENT "qr code обуви" AFTER `product_barcode`');
	}

	public function down()
	{
		$this->dropColumn('{{%ecommerce_stock}}','product_qrcode');

	}
}
