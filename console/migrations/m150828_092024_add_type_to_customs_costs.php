<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_092024_add_type_to_customs_costs extends Migration
{
    public function up()
    {
        $this->addColumn('{{%customs_account_costs}}', 'cost_type', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 comment "Тип расхода" AFTER  `customs_accounts_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%customs_account_costs}}', 'cost_type');

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
