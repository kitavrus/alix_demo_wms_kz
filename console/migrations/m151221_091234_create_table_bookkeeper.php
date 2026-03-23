<?php

use yii\db\Schema;
use yii\db\Migration;

class m151221_091234_create_table_bookkeeper extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%bookkeeper}}', [
            'id' => Schema::TYPE_PK,
            'tl_delivery_proposal_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0 COMMENT "Заявка на доставку"',
            'tl_delivery_proposal_route_unforeseen_expenses_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0 COMMENT "Расход по маршруту"',
            'department_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0 COMMENT "Отдел: склад, трнспорт"',
            'doc_type_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0 COMMENT "Тип документа: чек, счет-фак, тд"',
            'doc_file' =>  Schema::TYPE_STRING . '(256)  NULL DEFAULT "" COMMENT "Путь к файлу документа"',

            'name_supplier' => Schema::TYPE_STRING . '(128) NULL DEFAULT "" COMMENT "Название поставщика"',
            'description' => Schema::TYPE_STRING . '(228) NULL DEFAULT "" COMMENT "Описание засхода"',

            'plus_sum' => Schema::TYPE_DECIMAL . "(26,3)  NULL DEFAULT '0' COMMENT 'Поступление денег'",
            'minus_sum' => Schema::TYPE_DECIMAL . "(26,3)  NULL DEFAULT '0' COMMENT 'Расход'",
            'balance_sum' => Schema::TYPE_DECIMAL . "(26,3)  NULL DEFAULT '0' COMMENT 'Остаток'",

            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'show, hide'",
            'date_at' => Schema::TYPE_INTEGER . '  NULL  DEFAULT "0" COMMENT "Дата"',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0',

            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%bookkeeper}}');
    }
}