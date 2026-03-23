<?php

use yii\db\Schema;
use yii\db\Migration;

class m240909_095138_add_outbound_and_crossdock_print_out_status extends Migration
{
	public function up()
	{
		$this->addColumn(
			'{{%outbound_orders}}',
			'print_outbound_status',
			Schema::TYPE_STRING . ' DEFAULT "no" COMMENT "print out status" AFTER `status`');

		$this->addColumn(
			'{{%cross_dock}}',
			'print_outbound_status',
			Schema::TYPE_STRING . ' DEFAULT "no" COMMENT "print out status" AFTER `status`');
	}

	public function down()
	{
		$this->dropColumn('{{%outbound_orders}}','print_outbound_status');
		$this->dropColumn('{{%cross_dock}}','print_outbound_status');

	}
}
