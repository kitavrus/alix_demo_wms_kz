<?php

use yii\db\Schema;
use yii\db\Migration;

class m140916_092228_create_new_table_tl_city extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%city}}', [
            'id' => Schema::TYPE_PK,

            'name' => Schema::TYPE_STRING . '(64) NULL comment "City name"',
            'region_id' => Schema::TYPE_INTEGER . '(11) NULL comment "Region id"',

            'comment' => Schema::TYPE_TEXT . '  NULL',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%city}}');
    }
}
