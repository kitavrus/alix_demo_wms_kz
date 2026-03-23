<?php

use yii\db\Schema;
use yii\db\Migration;

class m150320_053847_create_table_outbound_upload_log extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%outbound_upload_log}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Client id"',

            'unique_key' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Unique key if update exist order"',
            'party_number' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Outbound party number"',
            'order_number' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Outbound order number"',
            'product_barcode' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product barcode"',
            'product_model' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product model"',
            'expected_qty' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT "Expected qty"',

            'from_point_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal from point id"',
            'to_point_id' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Internal to point id"',

            'to_point_title' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product model"',
            'from_point_title' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product model"',
            'data_created_on_client' => Schema::TYPE_STRING . '(64)  NULL DEFAULT "" comment "Date time created order on client"',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
        ], $tableOptions);

//        $this->addColumn('{{%outbound_orders}}','from_point_id',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal from point id" AFTER `warehouse_id`');
//        $this->addColumn('{{%outbound_orders}}','to_point_id',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal to point id" AFTER `from_point_id`');
//
//        $this->addColumn('{{%outbound_orders}}','to_point_title',Schema::TYPE_STRING . '(256) NULL DEFAULT "" COMMENT  "Internal from point text value" AFTER `to_point_id`');
//        $this->addColumn('{{%outbound_orders}}','from_point_title',Schema::TYPE_STRING . '(256) NULL DEFAULT "" COMMENT  "Internal from point text value" AFTER `to_point_title`');
//
    }

    public function down()
    {
        $this->dropTable('{{%outbound_upload_log}}');

        return true;
    }
}
