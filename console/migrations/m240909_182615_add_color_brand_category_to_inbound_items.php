<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240909_182615_add_color_brand_category_to_stock
 */
class m240909_182615_add_color_brand_category_to_inbound_items extends Migration
{
	public function up()
	{
		$this->addColumn('{{%inbound_order_items}}','product_color', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "" AFTER `product_barcode`');
		$this->addColumn('{{%inbound_order_items}}','product_brand', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "" AFTER `product_color`');
		$this->addColumn('{{%inbound_order_items}}','product_category', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "T-shirt,shoes" AFTER `product_brand`');
	}

	public function down()
	{
		$this->dropColumn('{{%inbound_order_items}}','product_color');
		$this->dropColumn('{{%inbound_order_items}}','product_brand');
		$this->dropColumn('{{%inbound_order_items}}','product_category');

	}
}
