<?php

use yii\db\Schema;
use yii\db\Migration;

class m150115_082143_add_client_settings_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%client_settings}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Client ID"',
            'option_name' => Schema::TYPE_TEXT . ' NULL COMMENT "Option name"',
            'option_value' => Schema::TYPE_TEXT . ' NULL COMMENT "Option value"',
            'default_value' => Schema::TYPE_TEXT . ' NULL COMMENT "Default value for this options"',
            'description' => Schema::TYPE_TEXT . '  NULL COMMENT "Option description"',
            'option_type' => Schema::TYPE_SMALLINT . ' NULL COMMENT "Type: function, dropdown etc"',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%client_settings}}');
    }
}
