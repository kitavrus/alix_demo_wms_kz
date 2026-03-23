<?php

use yii\db\Schema;
use yii\db\Migration;

class m150427_031457_create_table_box_size extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%box_size}}', [
            'id' => Schema::TYPE_PK,

            'box_height' => Schema::TYPE_STRING . '(4) NULL DEFAULT "" COMMENT "Height"', // Высота
            'box_width' => Schema::TYPE_STRING . '(4) NULL DEFAULT "" COMMENT "Width"', // Ширина
            'box_length' => Schema::TYPE_STRING . '(4) NULL DEFAULT "" COMMENT "Length"', // Длина
            'box_code' => Schema::TYPE_STRING . '(4) NULL DEFAULT "" COMMENT "Box code"', // Код

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%box_size}}');
    }
}
