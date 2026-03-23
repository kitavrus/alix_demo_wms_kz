<?php

use yii\db\Schema;
use yii\db\Migration;

class m160113_082755_create_table_tl_agents_bookkeeper extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_agents_bookkeeper}}', [
            'id' => Schema::TYPE_PK,
            'agent_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0 COMMENT "Заявка на доставку"',

            'name' => Schema::TYPE_STRING . '(128) NULL DEFAULT "" COMMENT "Название поставщика"',
            'description' => Schema::TYPE_STRING . '(228) NULL DEFAULT "" COMMENT "Описание засхода"',

            'invoice' => Schema::TYPE_DECIMAL . "(26,3)  NULL DEFAULT '0' COMMENT 'Сумма счета'",

            'month_from' => Schema::TYPE_STRING . "(64)  NULL DEFAULT '' COMMENT 'Счет с'",
            'month_to' => Schema::TYPE_STRING . "(64)  NULL DEFAULT '' COMMENT 'Счет по'",

            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Счет веставлен, счет оплачен'",

            'date_of_invoice' => Schema::TYPE_INTEGER . '  NULL  DEFAULT "0" COMMENT "Дата выставления счета"',
            'payment_date_invoice' => Schema::TYPE_INTEGER . '  NULL  DEFAULT "0" COMMENT "Дата оплаты счета"',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0',

            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_agents_bookkeeper}}');
    }
}