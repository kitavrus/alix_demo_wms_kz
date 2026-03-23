<?php

use yii\db\Schema;
use yii\db\Migration;

class m150303_134830_add_delivery_proposal_registry_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%delivery_proposal_registry}}', [
            'id' => Schema::TYPE_PK,
            'dp_list' => Schema::TYPE_STRING . ' NOT NULL COMMENT "Proposals IDs"',
            'registry_type' =>  Schema::TYPE_INTEGER . ' NULL COMMENT "Type of registry"',
            'status' => Schema::TYPE_INTEGER . ' NULL',
            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',

        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('delivery_proposal_registry');

        return true;
    }
}
