<?php

use yii\db\Schema;
use yii\db\Migration;

class m150814_093416_create_table_customs_account_costs extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%customs_account_costs}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . '(128) DEFAULT "" COMMENT "Название"',
            'price_cost_our' => Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Расходы наши"',
            'price_nds_cost_our' => Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Расходы наши с ндс"',

            'price_cost_client' => Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Выставляем счет клиенту"',
            'price_nds_cost_client' => Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Выставляем счет клиенту с ндс"',

            'payment_status' => Schema::TYPE_INTEGER.'(11) NULL DEFAULT 0  COMMENT "Счет Новый, Выставлен, Оплачен"',

            'comments' => Schema::TYPE_TEXT.' NULL DEFAULT "" COMMENT "Комментарий"',

//            'status' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%customs_account_costs}}');
    }
}