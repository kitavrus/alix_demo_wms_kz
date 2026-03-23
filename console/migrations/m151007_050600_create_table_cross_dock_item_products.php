<?php

use yii\db\Schema;
use yii\db\Migration;

class m151007_050600_create_table_cross_dock_item_products extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cross_dock_item_products}}', [
            'id' => Schema::TYPE_PK,
            'cross_dock_item_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Internal cross dock order id"',

            'product_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Internal product id"',
            'product_name' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Scanned product name"',
            'product_barcode' => Schema::TYPE_STRING . '(54)  NULL COMMENT "Scanned product barcode"',
            'product_price' => Schema::TYPE_DECIMAL . '(16,3) NULL COMMENT "Product price"',
            'product_model' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product model"',
            'product_sku' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product sku"',
            'product_madein' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product made in"',
            'product_composition' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product composition"',
            'product_exporter' => Schema::TYPE_TEXT . ' NULL COMMENT "Product exporter"',
            'product_importer' => Schema::TYPE_TEXT . ' NULL COMMENT "Product importer"',
            'product_description' => Schema::TYPE_TEXT . ' NULL COMMENT "Product importer"',
            'product_serialize_data' => Schema::TYPE_TEXT . ' NULL COMMENT "Product serialize data"',

            'box_barcode' => Schema::TYPE_STRING . '(54)  NULL COMMENT "Box barcode"',

            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Status new, scanned'",

            'expected_qty' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" COMMENT "Expected product quantity in order"',
            'accepted_qty' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" COMMENT "Accepted product quantity in order"',

            'begin_datetime' => Schema::TYPE_INTEGER . ' NULL  COMMENT "The start time of the scan order"',
            'end_datetime' => Schema::TYPE_INTEGER . '  NULL  COMMENT "The end time of the scan order"',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',

            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%cross_dock_item_products}}');
    }
}
