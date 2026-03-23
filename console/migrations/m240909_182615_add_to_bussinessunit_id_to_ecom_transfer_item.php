<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240909_182615_add_to_bussinessunit_id_to_ecom_transfer_item
 */
class m240909_182615_add_to_bussinessunit_id_to_ecom_transfer_item extends Migration
{
	public function up()
	{
		$this->addColumn('{{%ecommerce_transfer_items}}','client_ToBusinessUnitId', Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "Client store code" AFTER `client_Status`');
	}

	public function down()
	{
		$this->dropColumn('{{%ecommerce_transfer_items}}','client_ToBusinessUnitId');

	}
}
