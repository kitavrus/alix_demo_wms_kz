<?php

use yii\db\Schema;
use yii\db\Migration;

class m150720_123154_update_agent_billing_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->dropTable('tl_agents_billing');
        $this->dropTable('tl_agents_billing_conditions');

        $this->createTable('{{%tl_agents_billing}}', [
            'id' => Schema::TYPE_PK,
            'agent_id' => Schema::TYPE_INTEGER . ' NULL comment "Agent id"',  //субподрядчик
            'status' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0', // Статус, выпадающий писок. Пример значенией : Активен не активен
            'cash_no' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0', // Нал\безнал
            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',// Это заполняется через бихейвер
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL', // Это заполняется через бихейвер
            'created_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
            'updated_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0', // Это заполняется через бихейвер
        ], $tableOptions);


        $this->createTable('{{%tl_agents_billing_conditions}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_TEXT . '  NULL', //название
            'tl_agents_billing_id' => Schema::TYPE_INTEGER . ' NULL', //тариф
            'agent_id' => Schema::TYPE_INTEGER . ' NULL',  //субподрядчик
            'formula_tariff' => Schema::TYPE_TEXT . ' NULL comment "Formula for tariff"', // Формула для вычисления тарифа
            'rule_tyle' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0', //тип подсчета
            'route_from' => Schema::TYPE_INTEGER . ' NULL', //откуда (точка)
            'route_to' => Schema::TYPE_INTEGER . ' NULL',  //куда(точка)
            'price_invoice' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Sale for client"', //цена фиксированная без НДС
            'price_invoice_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price invoice with NDS"',//цена фиксированная с НДС
            'price_kg' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price kg without NDS"', //цена за КГ без НДС
            'price_kg_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price kg with NDS"',//цена за КГ с НДС
            'price_mc' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price mc without NDS"', //цена за кубометр без НДС
            'price_mc_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price mc with NDS"',//цена за кубометр с НДС
            'price_pl' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price pl without NDS"', //цена за место без НДС
            'price_pl_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price pl with NDS"',//цена за место с НДС
            'status' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0', //статус
            'comment' => Schema::TYPE_TEXT . '  NULL', //комменетарий

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('tl_agents_billing');
        $this->dropTable('tl_agents_billing_conditions');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
