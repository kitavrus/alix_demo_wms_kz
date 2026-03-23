<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m241216_162010_add_stock_ecom_outbound
 */
class m241216_162010_add_stock_ecom_outbound extends Migration
{
	public function up()
	{
		$this->addColumn('{{%stock}}','ecom_outbound_id', Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "Ecom outbound id" AFTER `client_id`');
		$this->addColumn('{{%stock}}','ecom_outbound_items_id', Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "Ecom outbound item id" AFTER `ecom_outbound_id`');
	}

	public function down()
	{
		$this->dropColumn('{{%stock}}','ecom_outbound_id');
		$this->dropColumn('{{%stock}}','ecom_outbound_items_id');
	}
}
