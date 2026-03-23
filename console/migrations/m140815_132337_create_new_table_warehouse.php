<?php

use yii\db\Schema;
use yii\db\Migration;

class m140815_132337_create_new_table_warehouse extends Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%warehouse}}', [
			'id' => Schema::TYPE_PK,
			'name' => Schema::TYPE_STRING . '(128) NOT NULL',
			'description' => Schema::TYPE_TEXT . ' NOT NULL',
			'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
			'country' => Schema::TYPE_STRING . '(128) NOT NULL',
			'region' => Schema::TYPE_STRING . '(128) NOT NULL',
			'city' => Schema::TYPE_STRING . '(128) NOT NULL',
			'zip_code' => Schema::TYPE_STRING . '(9) NOT NULL',
			'street' => Schema::TYPE_STRING . '(128) NOT NULL',
			'house' => Schema::TYPE_STRING . '(6) NOT NULL',

			'created_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
			'updated_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',

			'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
			'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
		], $tableOptions);
	}

	public function down()
	{
		$this->dropTable('{{%warehouse}}');
	}
}
