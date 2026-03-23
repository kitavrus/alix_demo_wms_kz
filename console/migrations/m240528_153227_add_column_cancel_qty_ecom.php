<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240528_153227_add_column_cancel_qty_ecom
 */
class m240528_153227_add_column_cancel_qty_ecom extends Migration
{
	public function up()
	{
		$this->addColumn('{{%ecommerce_outbound_items}}','cancel_qty', Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "отмененное количество" AFTER `accepted_qty`');
	}

	public function down()
	{
		$this->dropColumn('{{%ecommerce_outbound_items}}','cancel_qty');

	}
}
