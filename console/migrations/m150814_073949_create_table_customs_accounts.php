<?php

use yii\db\Schema;
use yii\db\Migration;

class m150814_073949_create_table_customs_accounts extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%customs_accounts}}', [
            'id' => Schema::TYPE_PK,
            'kg_netto' => Schema::TYPE_INTEGER.'(11) NULL DEFAULT 0  COMMENT "Вес нетто"',
            'kg_brutto' => Schema::TYPE_INTEGER.'(11) NULL DEFAULT 0 COMMENT "Вес брутто"',
            'invoice_number' => Schema::TYPE_STRING . '(128) DEFAULT "" COMMENT "Инвойс"',
            'qty_place' => Schema::TYPE_INTEGER.'(11) NULL DEFAULT 0 COMMENT "Количество мест"',
            'qty_tnv_codes' => Schema::TYPE_INTEGER.'(11) NULL DEFAULT 0 COMMENT "ТНВ коды, общее кол-во"',
            'price' => Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Цена"',
            'price_nds' => Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Цена с ндс"',
            'comments' => Schema::TYPE_TEXT.' NULL DEFAULT "" COMMENT "Комментарий"',

            'status' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%customs_accounts}}');
    }

}