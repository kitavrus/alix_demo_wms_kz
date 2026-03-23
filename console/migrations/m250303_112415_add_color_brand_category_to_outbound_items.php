<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m250303_112415_add_color_brand_category_to_outbound_items
 */
class m250303_112415_add_color_brand_category_to_outbound_items extends Migration
{
	public function up()
	{
		$this->addColumn('{{%outbound_order_items}}','product_color', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "" AFTER `product_barcode`');
		$this->addColumn('{{%outbound_order_items}}','product_brand', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "" AFTER `product_color`');
		$this->addColumn('{{%outbound_order_items}}','product_category', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "T-shirt,shoes" AFTER `product_brand`');
	}

	public function down()
	{
		$this->dropColumn('{{%outbound_order_items}}','product_color');
		$this->dropColumn('{{%outbound_order_items}}','product_brand');
		$this->dropColumn('{{%outbound_order_items}}','product_category');

	}
}
