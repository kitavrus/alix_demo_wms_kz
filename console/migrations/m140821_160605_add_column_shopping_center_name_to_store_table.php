<?php

use yii\db\Schema;
use yii\db\Migration;

class m140821_160605_add_column_shopping_center_name_to_store_table extends Migration
{
	public function up()
	{
		$this->addColumn('{{%store}}','shopping_center_name',Schema::TYPE_STRING . '(128) NOT NULL COMMENT "Shopping center name. Example: Master " AFTER `name`');
	}

	public function down()
	{
		$this->dropColumn('{{%store}}','shopping_center_name');
	}
}