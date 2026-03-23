<?php

use yii\db\Migration;

/**
 * Class m241023_162010_change_products
 */
class m241023_162010_change_products extends Migration
{
	public function up()
	{
		$this->execute("ALTER TABLE `product` CHANGE `model` `model` varchar(255) NULL DEFAULT ''");
	}

	public function down()
	{
	}
}
