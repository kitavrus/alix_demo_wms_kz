<?php

use yii\db\Schema;
use yii\db\Migration;

class m150223_041101_create_table_base_barcodes extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%base_barcodes}}', [
            'id' => Schema::TYPE_PK,

            'order_type' => Schema::TYPE_INTEGER . ' NULL COMMENT "Type: inbound, outbound, cargo pick-up"',

            'base_barcode' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Base barcode"',
            'box_barcode' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Box barcode"',
            'box_number' => Schema::TYPE_INTEGER . '(4) NULL DEFAULT "0" COMMENT "Box number"',
            'ttn_barcode' => Schema::TYPE_INTEGER . '(34) NULL DEFAULT "0" COMMENT "TTN"',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%base_barcodes}}');
    }
}
