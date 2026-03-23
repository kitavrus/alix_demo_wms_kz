<?php

use yii\db\Schema;
use yii\db\Migration;

class m140923_095138_add_fields_to_table_tl_delivery_proposal_order_extras extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_proposal_order_extras}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NOT NULL comment "Example: DeFacto. Internal client id"',
            'tl_delivery_proposal_id' => Schema::TYPE_INTEGER . ' NULL comment "DP id"',
            'tl_delivery_route_id' => Schema::TYPE_INTEGER . ' NULL comment "DP route id"',
            'tl_delivery_proposal_order_id' => Schema::TYPE_INTEGER . ' NULL comment "DP route order id"',
            'name' => Schema::TYPE_STRING . ' NULL',
            'number_places' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" comment "Estimated number palaces"', // Перполагаемое количество мест. обычный инпут

            'comment' => Schema::TYPE_TEXT . ' NULL',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_proposal_order_extras}}');
    }
}
