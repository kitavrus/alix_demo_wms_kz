<?php

use yii\db\Schema;
use yii\db\Migration;

class m150722_092248_create_table_tl_delivery_proposal_default_sub_routes extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_proposal_default_sub_routes}}', [
            'id' => Schema::TYPE_PK,
            'tl_delivery_proposal_default_route_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "DP route id"',
            'client_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Client store id"',
            'from_point_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "From point id"',
            'to_point_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "To point id"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',

            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_proposal_default_sub_routes}}');
    }
}