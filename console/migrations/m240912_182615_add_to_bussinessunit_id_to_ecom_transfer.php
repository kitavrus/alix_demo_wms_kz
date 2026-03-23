<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240912_182615_add_to_bussinessunit_id_to_ecom_transfer
 */
class m240912_182615_add_to_bussinessunit_id_to_ecom_transfer extends Migration
{
	public function up()
	{
		$this->addColumn('{{%ecommerce_transfer}}','client_ToBusinessUnitId', Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "Client store code" AFTER `client_LcBarcode`');
	}

	public function down()
	{
		$this->dropColumn('{{%ecommerce_transfer}}','client_ToBusinessUnitId');

	}
}
