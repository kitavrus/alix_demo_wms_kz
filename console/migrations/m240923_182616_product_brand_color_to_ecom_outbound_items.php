<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240923_182616_product_brand_color_to_ecom_outbound_items
 */
class m240923_182616_product_brand_color_to_ecom_outbound_items extends Migration
{
	public function up()
	{
		$this->addColumn('{{%ecommerce_outbound_items}}','product_brand', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Product brand" AFTER `product_barcode`');
		$this->addColumn('{{%ecommerce_outbound_items}}','product_color', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Product color" AFTER `product_brand`');
	}

	public function down()
	{
		$this->dropColumn('{{%ecommerce_outbound_items}}','product_brand');
		$this->dropColumn('{{%ecommerce_outbound_items}}','product_color');
	}
}
