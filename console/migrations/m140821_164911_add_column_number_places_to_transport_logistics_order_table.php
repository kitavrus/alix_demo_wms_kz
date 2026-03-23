<?php

use yii\db\Schema;
use yii\db\Migration;

class m140821_164911_add_column_number_places_to_transport_logistics_order_table extends Migration
{
	public function up()
	{
		$this->addColumn('{{%transport_logistics_order}}','number_places',Schema::TYPE_INTEGER . '(11) NOT NULL COMMENT "Number of places" AFTER `kg`');
	}

	public function down()
	{
		$this->dropColumn('{{%transport_logistics_order}}','number_places');
	}
}
