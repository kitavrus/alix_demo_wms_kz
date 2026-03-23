<?php

use yii\db\Schema;
//use yii\db\Migration;
use dektrium\user\migrations\Migration;

class m140915_043746_create_token_client_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%client_token}}', [
            'client_id'    => Schema::TYPE_INTEGER . ' NOT NULL',
            'code'       => Schema::TYPE_STRING . '(32) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type'       => Schema::TYPE_SMALLINT . ' NOT NULL'
        ], $this->tableOptions);

        $this->createIndex('client_token_unique', '{{%client_token}}', ['client_id', 'code', 'type'], true);
//        $this->addForeignKey('fk_client_token', '{{%token}}', 'client_id', '{{%client}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%client_token}}');
    }
}
