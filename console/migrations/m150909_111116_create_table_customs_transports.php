<?php

use yii\db\Schema;
use yii\db\Migration;

class m150909_111116_create_table_customs_transports extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%customs_transports}}', [
            'id' => Schema::TYPE_PK,
            'customs_order_id' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Заявка"',
            'type_id' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Тип: авто,жд,авиа"',

            'avia_order_number' => Schema::TYPE_STRING . '(256) NULL DEFAULT "" COMMENT "авиа накладная"',
            'avia_arrival_departure_time' => Schema::TYPE_STRING . '(256) NULL DEFAULT "" COMMENT "время вылета и прилета"',
            'avia_status_document' => Schema::TYPE_INTEGER . '(11) NULL COMMENT "Статус документов"',

            'auto_number' => Schema::TYPE_STRING . '(256) NULL DEFAULT "" COMMENT "Номер машины"',
            'auto_status' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Статус машины"',
            'auto_status_document' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Статус документов"',

            'railroad_number' => Schema::TYPE_STRING . '(256) NULL DEFAULT "" COMMENT "Номер жд накладной"',
            'railroad_status' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Статус жд"',
            'railroad_status_document' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Статус документов"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%customs_transports}}');
    }
}
