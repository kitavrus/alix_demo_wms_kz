<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m240422_200700_inbound_order_items_add_brand_size
 */
class m240422_200700_inbound_order_items_add_brand_size extends Migration
{
	public function up()
	{
		$this->addColumn('{{%inbound_order_items}}','product_size', Schema::TYPE_STRING . '(1024) NULL DEFAULT "" COMMENT "" AFTER `product_serialize_data`');
		$this->addColumn('{{%inbound_order_items}}','product_brand', Schema::TYPE_STRING . '(1024) NULL DEFAULT "" COMMENT "" AFTER `product_size`');
	}

	public function down()
	{
		$this->dropColumn('{{%inbound_order_items}}','product_size');
		$this->dropColumn('{{%inbound_order_items}}','product_brand');

	}
}
