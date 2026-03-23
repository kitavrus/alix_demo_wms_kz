<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m250519_182615_cron_manager
 */
class m250519_182615_cron_manager extends Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('cron_manager', [
			'id' => $this->primaryKey(),
			'name' => $this->string(128)->defaultValue('')->comment("Название задачи"),
			'order_id' =>$this->integer(11)->defaultValue(0)->comment("Id закрываемой накладной"),
			'status' => $this->string(128)->defaultValue('NEW')->comment("Статус"),
			'type' => $this->string(128)->defaultValue('')->comment("b2c-in,b2b-in,b2b-re"),
			'result_message' => $this->text()->defaultValue('')->comment("Сообщение от сервиса"),
			'total_counter' =>$this->integer(11)->defaultValue(0)->comment("Счетчик по достижению которого можно закрывать накладную"),
			'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
			'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
			'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
			'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
			'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
		],$tableOptions);

	}

	public function down()
	{
		$this->dropTable('{{%cron_manager}}');
	}
}
