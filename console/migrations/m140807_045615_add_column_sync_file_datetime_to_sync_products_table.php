<?php

use yii\db\Schema;
use yii\db\Migration;

class m140807_045615_add_column_sync_file_datetime_to_sync_products_table extends Migration
{
	public function up()
	{
		$this->addColumn('{{%sync_products}}','sync_file_datetime',Schema::TYPE_STRING . '(64) NULL COMMENT "Last datetime update file" AFTER `modified_user_id`');
	}

	public function down()
	{
		$this->dropColumn('{{%sync_products}}','sync_file_datetime');
	}
}
