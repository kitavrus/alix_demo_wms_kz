<?php

use yii\db\Schema;
use yii\db\Migration;

class m140917_101524_create_table_tl_delivery_proposal_route_unforeseen_expenses extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_proposal_route_unforeseen_expenses}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NOT NULL comment "Example: DeFacto. Internal client id"',
            'tl_delivery_proposal_id' => Schema::TYPE_INTEGER . ' NULL comment "DP id"',
            'tl_delivery_route_id' => Schema::TYPE_INTEGER . ' NULL comment "DP route id"',
            'name' => Schema::TYPE_STRING . ' NULL',
            'delivery_date' => Schema::TYPE_INTEGER . ' NULL comment "Data expenses"',
            'price' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price expenses"',
            'cash_no' => Schema::TYPE_SMALLINT . ' NULL DEFAULT "0" comment "nal/bez"',
            'with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "C NDS"',
            'status' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

            'comment' => Schema::TYPE_TEXT . ' NULL',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_proposal_route_unforeseen_expenses}}');
    }
}
