<?php

use yii\db\Migration;

/**
 * Class m210929_102309_change_address_place
 */
class m210929_102309_change_address_place extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up()
	{
		$this->createTable('change_address_place', [
			'id' => $this->primaryKey(),
			'from_barcode' => $this->string(16)->defaultValue('')->comment("Address/Box barcode"),
			'to_barcode' => $this->string(16)->defaultValue('')->comment("Address/Box barcode"),
			'change_type' =>$this->smallInteger()->defaultValue(0)->comment("Change type"),
			'message' => $this->string(512)->defaultValue('')->comment("Message"),
			'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
			'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
			'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
			'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
			'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function down()
	{
		$this->dropTable('change_address_place');
	}
}
