<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240909_182615_add_color_brand_category_to_stock
 */
class m240909_182615_add_color_brand_category_to_stock extends Migration
{
	public function up()
	{
		$this->addColumn('{{%stock}}','product_color', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "" AFTER `product_barcode`');
		$this->addColumn('{{%stock}}','product_brand', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "" AFTER `product_color`');
		$this->addColumn('{{%stock}}','product_category', Schema::TYPE_STRING . ' DEFAULT "" COMMENT "T-shirt,shoes" AFTER `product_brand`');
	}

	public function down()
	{
		$this->dropColumn('{{%stock}}','product_color');
		$this->dropColumn('{{%stock}}','product_brand');
		$this->dropColumn('{{%stock}}','product_category');

	}
}
