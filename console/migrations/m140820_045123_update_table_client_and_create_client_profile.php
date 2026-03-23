<?php

use yii\db\Schema;
use yii\db\Migration;

class m140820_045123_update_table_client_and_create_client_profile extends Migration
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

        $this->dropTable('{{%client}}');

        $this->createTable('{{%client}}', [
            'id'            => Schema::TYPE_PK,
            'username'      => Schema::TYPE_STRING . '(25) NOT NULL',
            'email'         => Schema::TYPE_STRING . '(255) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . '(60) NOT NULL',
            'auth_key'      => Schema::TYPE_STRING . '(32) NOT NULL',

            // confirmation
            'confirmation_token'   => Schema::TYPE_STRING . '(32)',
            'confirmation_sent_at' => Schema::TYPE_INTEGER,
            'confirmed_at'         => Schema::TYPE_INTEGER,
            'unconfirmed_email'    => Schema::TYPE_STRING . '(255)',

            // recovery
            'recovery_token'   => Schema::TYPE_STRING . '(32)',
            'recovery_sent_at' => Schema::TYPE_INTEGER,

            // block
            'blocked_at' => Schema::TYPE_INTEGER,

            // RBAC
            'role' => Schema::TYPE_STRING . '(255)',

            // trackable
            'registered_from' => Schema::TYPE_INTEGER,
            'logged_in_from'  => Schema::TYPE_INTEGER,
            'logged_in_at'    => Schema::TYPE_INTEGER,

            // timestamps
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->createIndex('client_unique_username', '{{%client}}', 'username', true);
        $this->createIndex('client_unique_email', '{{%client}}', 'email', true);
        $this->createIndex('client_confirmation', '{{%client}}', 'id, confirmation_token', true);
        $this->createIndex('client_recovery', '{{%client}}', 'id, recovery_token', true);

        $this->createTable('{{%client_profile}}', [
            'user_id'        => Schema::TYPE_INTEGER . ' PRIMARY KEY',
            'name'           => Schema::TYPE_STRING . '(255)',
            'public_email'   => Schema::TYPE_STRING . '(255)',
            'gravatar_email' => Schema::TYPE_STRING . '(255)',
            'gravatar_id'    => Schema::TYPE_STRING . '(32)',
            'location'       => Schema::TYPE_STRING . '(255)',
            'website'        => Schema::TYPE_STRING . '(255)',
            'bio'            => Schema::TYPE_TEXT
        ], $tableOptions);

        $this->addForeignKey('fk_client_profile', '{{%client_profile}}', 'user_id', '{{%client}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%client_profile}}');
        $this->dropTable('{{%client}}');
    }
}
