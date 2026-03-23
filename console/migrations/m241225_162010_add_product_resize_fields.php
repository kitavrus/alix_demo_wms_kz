<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m241225_162010_add_product_resize_fields
 */
class m241225_162010_add_product_resize_fields extends Migration
{
	public function up()
	{
		$this->execute("
		ALTER TABLE `product`
CHANGE `client_product_id` `client_product_id` varchar(256) COLLATE \"utf8_general_ci' NOT NULL AFTER `client_id`,
CHANGE `color` `color` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `model`,
CHANGE `size` `size` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `color`,
CHANGE `season` `season` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `size`,
CHANGE `category` `category` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `composition`,
CHANGE `gender` `gender` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `category`,
CHANGE `field_extra1` `field_extra1` varchar(256) COLLATE 'utf8_general_ci' NULL DEFAULT '' COMMENT 'Extra field 1\" AFTER `barcode`;
		");
	}

	public function down()
	{
	}
}
