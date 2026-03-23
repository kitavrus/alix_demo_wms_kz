<?php

use yii\db\Schema;
use yii\db\Migration;

class m140820_045748_create_table_client_account extends Migration
{
    public function up()
    {
        switch (Yii::$app->db->driverName) {
            case 'mysql':
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
                break;
            case 'pgsql':
                $tableOptions = null;
                break;
            default:
                throw new RuntimeException('Your database is not supported!');
        }

        $this->createTable('{{%client_account}}', [
            'id'         => Schema::TYPE_PK,
            'user_id'    => Schema::TYPE_INTEGER,
            'provider'   => Schema::TYPE_STRING . ' NOT NULL',
            'client_id'  => Schema::TYPE_STRING . ' NOT NULL',
            'properties' => Schema::TYPE_TEXT
        ], $tableOptions);

        $this->createIndex('account_unique', '{{%client_account}}', ['provider', 'client_id'], true);
        $this->addForeignKey('fk_client_account', '{{%client_account}}', 'user_id', '{{%client}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%client_account}}');
    }
}
