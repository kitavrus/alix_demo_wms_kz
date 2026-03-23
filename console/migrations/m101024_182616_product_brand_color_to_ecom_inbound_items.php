<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m101024_182616_product_brand_color_to_ecom_inbound_items
 */
class m101024_182616_product_brand_color_to_ecom_inbound_items extends Migration
{
	public function up()
	{
		$this->addColumn('{{%ecommerce_inbound_items}}','product_name', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Product brand" AFTER `product_barcode`');
		$this->addColumn('{{%ecommerce_inbound_items}}','product_brand', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Product brand" AFTER `product_name`');
		$this->addColumn('{{%ecommerce_inbound_items}}','product_color', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Product color" AFTER `product_brand`');
		$this->addColumn('{{%ecommerce_inbound_items}}','product_model', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "Product color" AFTER `product_color`');
	}

	public function down()
	{
		$this->dropColumn('{{%ecommerce_inbound_items}}','product_name');
		$this->dropColumn('{{%ecommerce_inbound_items}}','product_brand');
		$this->dropColumn('{{%ecommerce_inbound_items}}','product_color');
		$this->dropColumn('{{%ecommerce_inbound_items}}','product_model');
	}
}
