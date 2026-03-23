<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240610_182614_add_product_qrcode_stock
 */
class m240731_182615_add_pallet_address_stock extends Migration
{
	public function up()
	{
		$this->addColumn('{{%stock}}','pallet_address', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "qr code" AFTER `product_barcode`');
	}

	public function down()
	{
		$this->dropColumn('{{%stock}}','pallet_address');

	}
}
