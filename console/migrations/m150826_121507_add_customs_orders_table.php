<?php

use yii\db\Schema;
use yii\db\Migration;

class m150826_121507_add_customs_orders_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%customs_orders}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER.' NULL COMMENT "Клиент"',
            'tl_delivery_proposals_id' => Schema::TYPE_INTEGER.' NULL COMMENT "Заявка на доставку"',
            'customs_accounts_id' => Schema::TYPE_INTEGER.' NULL COMMENT "Таможенный счет"',
            'order_number' => Schema::TYPE_STRING.' NULL DEFAULT "" COMMENT "Номер заказа"',
            'status' => Schema::TYPE_SMALLINT.' NULL DEFAULT 0 COMMENT "Статус"',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',
        ], $tableOptions);

    }

    public function down()
    {
      $this->dropTable('{{%customs_orders}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}