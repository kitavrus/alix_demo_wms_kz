<?php

use yii\db\Schema;
use yii\db\Migration;

class m240906_095138_add_table_tl_delivery_proposal_order_boxes extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_proposal_order_boxes}}', [
            'id' => Schema::TYPE_PK,
			'tl_delivery_proposal_id' => Schema::TYPE_INTEGER . ' NULL comment "DP id"',
			'box_barcode' => Schema::TYPE_STRING . ' DEFAULT "" comment "Шк короба клиента"',
			'employee_name' => Schema::TYPE_STRING . ' DEFAULT "" comment "Имя сканирующего"',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_proposal_order_boxes}}');
    }
}
