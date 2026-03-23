<?php

use yii\db\Schema;
use yii\db\Migration;

class m140910_121006_create_new_table_tl_delivery_route_orders extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_route_orders}}', [
            'id' => Schema::TYPE_PK,
            'tl_delivery_route_id' => Schema::TYPE_INTEGER . ' NULL comment ""', // это поле будет заполнятся автоматически. значение будет бряться из tl_delivery_routes.
            'tl_delivery_proposal_id' => Schema::TYPE_INTEGER . ' NULL comment "Internal tl_delivery_proposal id"', // Это поле заполняется автоматически
            'client_id' => Schema::TYPE_INTEGER . ' NULL comment "Example: DeTacty. Internal client id"', // это поле будет заполнятся автоматически. значение будет бряться из tl_delivery_proposals.
            'order_type' => Schema::TYPE_INTEGER . ' NULL comment "Order type inbound or outbound"', //  Выпадающий список. Пример: приходная, расходная накладная
            'order_id' => Schema::TYPE_INTEGER . ' NULL comment "Order id"', //  Выпадающий список. В котором выбираются номера накладных указанного клиента

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',// Это заполняется через бихейвер
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL', // Это заполняется через бихейвер

            'created_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
            'updated_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_proposal_orders}}');
    }
}
