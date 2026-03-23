<?php

use yii\db\Schema;
use yii\db\Migration;

class m140926_103214_create_table_tl_delivery_proposal_routes_car extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_proposal_routes_car}}', [
            'id' => Schema::TYPE_PK,

            'tl_delivery_proposal_route_id' => Schema::TYPE_INTEGER . ' NULL comment "Dp route id"',
            'tl_delivery_proposal_route_cars_id' => Schema::TYPE_INTEGER . ' NULL comment "Dp route car id"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_proposal_routes_car}}');
    }
}
