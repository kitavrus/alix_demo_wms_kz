<?php

use yii\db\Schema;
use yii\db\Migration;

class m140821_164127_add_column_kg_name_to_transport_logistics_order_table extends Migration
{
	public function up()
	{
		$this->addColumn('{{%transport_logistics_order}}','kg',Schema::TYPE_DECIMAL . '(26,3) NOT NULL COMMENT "Kilogram" AFTER `mc`');
	}

	public function down()
	{
		$this->dropColumn('{{%transport_logistics_order}}','kg');
	}
}
