<?php

use yii\db\Schema;
use yii\db\Migration;

class m140821_170239_add_column_kg_actual__m3_actual_to_transport_logistics_order_table extends Migration
{
	public function up()
	{
		$this->addColumn('{{%transport_logistics_order}}','mc_actual',Schema::TYPE_DECIMAL . '(26,3) NOT NULL COMMENT "Kilogram" AFTER `mc`');
		$this->addColumn('{{%transport_logistics_order}}','kg_actual',Schema::TYPE_DECIMAL . '(26,3) NOT NULL COMMENT "Kilogram" AFTER `kg`');
	}

	public function down()
	{
		$this->dropColumn('{{%transport_logistics_order}}','kg_actual');
		$this->dropColumn('{{%transport_logistics_order}}','mc_actual');
	}
}
