<?php

use yii\db\Schema;
use yii\db\Migration;

class m140819_045506_create_new_table_transport_logistics_order extends Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%transport_logistics_order}}', [
			'id' => Schema::TYPE_PK,
			'client_id' => Schema::TYPE_INTEGER . ' NOT NULL comment "Example: DeTacty. Internal client id"',
			'route_from' => Schema::TYPE_INTEGER . ' NOT NULL comment "Example: DC-APORT"',
			'route_to' => Schema::TYPE_INTEGER . ' NOT NULL comment "Example: DC-APORT"',
			'delivery_date' => Schema::TYPE_INTEGER . ' NOT NULL comment "Date of planned delivery"',
			'mc' => Schema::TYPE_DECIMAL . '(26,3) NOT NULL DEFAULT "0" comment "Meters cubic"',
			'cross_doc' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Cross-doc"',
			'dc' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment ""',
			'hangers' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Hangers"',
			'other' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Other"',
			'auto_type' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Auto type: GAZ,Iveco"',
			'angar' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Angar"',
			'grzch' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Грзч"',
			'total_qty' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Грзч"',
			'price_square_meters' => Schema::TYPE_DECIMAL . '(26,3) NOT NULL DEFAULT "0" comment "Price m2"',
			'price_total' => Schema::TYPE_DECIMAL . '(26,3) NOT NULL DEFAULT "0" comment "Total"',
			'costs_region' => Schema::TYPE_DECIMAL . '(26,3) NOT NULL DEFAULT "0" comment "Costs in the region"',
			'agent_id' => Schema::TYPE_INTEGER . ' NOT NULL comment "Agent"',
			'cash_no' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT "0" comment "nal/bez"',
			'sale_for_client' => Schema::TYPE_INTEGER . ' NOT NULL comment "Sale for client"',
			'our_profit' => Schema::TYPE_DECIMAL . '(26,3) NOT NULL DEFAULT "0" comment "Our profit"',
			'costs_cache' => Schema::TYPE_DECIMAL . '(26,3) NOT NULL DEFAULT "0" comment "Expenses cash"',
			'with_vat' => Schema::TYPE_DECIMAL . '(26,3) NOT NULL DEFAULT "0" comment "C NDS"',
			'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
			'comment' => Schema::TYPE_TEXT . ' NOT NULL',

			'created_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
			'updated_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',

			'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
			'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
		], $tableOptions);
	}

	public function down()
	{
		$this->dropTable('{{%transport_logistics_order}}');
	}
}
