<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240923_182617_product_brand_color_to_ecom_stock_items
 */
class m240923_182617_product_brand_color_to_ecom_stock_items extends Migration
{
	public function up()
	{
		$this->addColumn('{{%ecommerce_stock}}','product_brand', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Product brand" AFTER `product_qrcode`');
		$this->addColumn('{{%ecommerce_stock}}','product_color', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Product color" AFTER `product_brand`');
	}

	public function down()
	{
		$this->dropColumn('{{%ecommerce_stock}}','product_brand');
		$this->dropColumn('{{%ecommerce_stock}}','product_color');
	}
}
