<?php

use yii\db\Schema;
use yii\db\Migration;

class m140821_165853_add_column_number_places_scanned_to_transport_logistics_order_table extends Migration
{
	public function up()
	{
		$this->addColumn('{{%transport_logistics_order}}','number_places_scanned',Schema::TYPE_INTEGER . '(11) NOT NULL COMMENT "Scanned number of places" AFTER `number_places`');
	}

	public function down()
	{
		$this->dropColumn('{{%transport_logistics_order}}','number_places_scanned');
	}
}
