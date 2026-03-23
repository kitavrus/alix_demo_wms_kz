<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m201102_074913_created_ecommerce_inventory
 */
class m201102_074913_created_ecommerce_inventory extends Migration
{
    public function up()
    {

//        SET NAMES utf8;
//SET time_zone = '+00:00';
//SET foreign_key_checks = 0;
//SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

//DROP TABLE IF EXISTS `inventory`;
$sql = "CREATE TABLE `ecommerce_inventory` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL COMMENT 'Client id',
  `order_number` varchar(54) DEFAULT '' COMMENT 'Inventory number',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `expected_places_qty` int(11) DEFAULT '0' COMMENT 'Expected place qty',
  `accepted_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted place qty',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $this->execute($sql);

$sql = "CREATE TABLE `ecommerce_inventory_rows` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) DEFAULT NULL COMMENT 'Inventory id',
  `column_number` int(11) DEFAULT '0' COMMENT 'Column number',
  `row_number` varchar(28) DEFAULT '',
  `level_number` int(11) NOT NULL DEFAULT '0',
  `floor_number` smallint(6) DEFAULT '0',
  `expected_qty` int(11) DEFAULT '0' COMMENT 'Expected qty',
  `accepted_qty` int(11) DEFAULT '0' COMMENT 'Accepted qty',
  `expected_places_qty` int(11) DEFAULT '0' COMMENT 'Expected place qty',
  `accepted_places_qty` int(11) DEFAULT '0' COMMENT 'Accepted place qty',
  `status` smallint(6) DEFAULT '0' COMMENT 'Status new, scanned',
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


        $this->execute($sql);

        $this->addColumn('{{%ecommerce_stock}}', 'inventory_id', Schema::TYPE_INTEGER . ' NULL DEFAULT 0 comment "" AFTER  `note_message2`');
        $this->addColumn('{{%ecommerce_stock}}', 'status_inventory', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 comment "" AFTER  `inventory_id`');
        $this->addColumn('{{%ecommerce_stock}}', 'inventory_box_address_barcode', Schema::TYPE_STRING . '(25) NULL DEFAULT "" comment "старый шк короба" AFTER  `status_inventory`');
        $this->addColumn('{{%ecommerce_stock}}', 'inventory_place_address_barcode', Schema::TYPE_STRING . '(25) NULL DEFAULT "" comment "старый адрес места" AFTER  `inventory_box_address_barcode`');

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_inventory}}');
        $this->dropTable('{{%ecommerce_inventory_rows}}');
    }
}
