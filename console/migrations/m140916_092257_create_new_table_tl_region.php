<?php

use yii\db\Schema;
use yii\db\Migration;

class m140916_092257_create_new_table_tl_region extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%region}}', [
            'id' => Schema::TYPE_PK,

            'name' => Schema::TYPE_STRING . '(64) NULL comment "Region name"',

            'comment' => Schema::TYPE_TEXT . '  NULL',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%region}}');
    }
}
