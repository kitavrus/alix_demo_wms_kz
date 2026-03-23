<?php

use yii\db\Schema;
use yii\db\Migration;

class m150119_034441_add_table_stock extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%stock}}', [
            'id' => Schema::TYPE_PK,
            'inbound_order_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT "Internal inbound order id"',
            'outbound_order_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT "Internal outbound order id"',
            'warehouse_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT "Internal warehouse order id"',

            'product_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Internal product id"',
            'product_name' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Scanned product name"',
            'product_barcode' => Schema::TYPE_STRING . '(54)  NULL COMMENT "Scanned product barcode"',
            'product_model' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product model"',
            'product_sku' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product sku"',
            'box_barcode' => Schema::TYPE_STRING . '(54)  NULL COMMENT "Box barcode"',

            'condition_type' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT '1=good,2=totally damaged, 3=partially damaged, 4 = lost item, 5 = par lost'",
            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Status new, scanned'",
            'stock_availability' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT '0 - Yes, 1 - No'",
            'primary_address' => Schema::TYPE_STRING . "(25) NULL DEFAULT '' COMMENT 'Box or pallet'",
            'secondary_address' => Schema::TYPE_STRING . "(25) NULL DEFAULT ''  COMMENT 'Polka'",

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',

            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%stock}}');
    }
}
