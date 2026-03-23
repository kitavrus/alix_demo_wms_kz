<?php

use yii\db\Schema;
use yii\db\Migration;

class m150825_092746_change_customs_price_field_type extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%customs_accounts}}', 'price', Schema::TYPE_DECIMAL.'(26,3) NULL DEFAULT 0 COMMENT "Стоимость"');
        $this->alterColumn('{{%customs_accounts}}', 'price_nds', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Стоимость с НДС"');
        $this->alterColumn('{{%customs_accounts}}', 'price_expenses_total', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Общая стоимость расходов"');
        $this->alterColumn('{{%customs_accounts}}', 'price_expenses_cache', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Наличные расходы"');
        $this->alterColumn('{{%customs_accounts}}', 'price_expenses_nds', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Безнал расходы"');
        $this->alterColumn('{{%customs_accounts}}', 'price_profit', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Доход"');

        $this->alterColumn('{{%customs_account_costs}}', 'price_cost_our', Schema::TYPE_DECIMAL.'(26,3) NULL DEFAULT 0 COMMENT "Наш расход"');
        $this->alterColumn('{{%customs_account_costs}}', 'price_nds_cost_our', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Наш расход с НДС"');
        $this->alterColumn('{{%customs_account_costs}}', 'price_cost_client', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Счет клиенту"');
        $this->alterColumn('{{%customs_account_costs}}', 'price_nds_cost_client', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Счет клиенту с НДС"');

    }

    public function down()
    {
        echo "m150825_092746_change_customs_price_field_type cannot be reverted.\n";

        return false;
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