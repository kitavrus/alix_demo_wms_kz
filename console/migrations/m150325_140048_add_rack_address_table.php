<?php

use yii\db\Schema;
use yii\db\Migration;

class m150325_140048_add_rack_address_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%rack_address}}', [
            'id' => Schema::TYPE_PK,
            'address' => Schema::TYPE_STRING . ' NULL COMMENT "Location coordinates"',
            'sort_order' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "Sort order"',
            'is_printed' => Schema::TYPE_SMALLINT . ' DEFAULT 0 COMMENT "Flag print or not"',
            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
        ], $tableOptions);
    }

    public function down()
    {
       $this->dropTable('rack_address');
    }
}
