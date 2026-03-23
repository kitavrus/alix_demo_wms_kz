<?php

use yii\db\Schema;
use yii\db\Migration;

class m150820_094923_update_customs_account_table extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%customs_accounts}}', 'kg_netto', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Вес нетто"');
        $this->alterColumn('{{%customs_accounts}}', 'kg_brutto', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Вес брутто"');

        $this->addColumn('{{%customs_accounts}}', 'price_expenses_total', Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Стоимость всех расходов" AFTER `price_nds`');
        $this->addColumn('{{%customs_accounts}}', 'price_expenses_cache', Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Стоимость расходов наличными" AFTER `price_expenses_total`');
        $this->addColumn('{{%customs_accounts}}', 'price_expenses_nds', Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Стоимость расходов c НДС" AFTER `price_expenses_cache`');
        $this->addColumn('{{%customs_accounts}}', 'price_profit', Schema::TYPE_DECIMAL.'(7,3) NULL DEFAULT 0 COMMENT "Доход" AFTER `price_expenses_nds`');
        $this->addColumn('{{%customs_account_costs}}', 'who_pay', Schema::TYPE_SMALLINT.' DEFAULT 0 COMMENT "Кто платит" AFTER `payment_status`');
        $this->addColumn('{{%customs_account_costs}}', 'customs_accounts_id', Schema::TYPE_INTEGER.' DEFAULT 0 COMMENT "Таможенный счет" AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%customs_accounts}}', 'kg_netto');
        $this->dropColumn('{{%customs_accounts}}', 'kg_brutto');

        $this->dropColumn('{{%customs_accounts}}', 'price_expenses_total');
        $this->dropColumn('{{%customs_accounts}}', 'price_expenses_cache');
        $this->dropColumn('{{%customs_accounts}}', 'price_expenses_nds');
        $this->dropColumn('{{%customs_accounts}}', 'price_profit');
        $this->dropColumn('{{%customs_account_costs}}', 'who_pay');
        $this->dropColumn('{{%customs_account_costs}}', 'customs_accounts_id');
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