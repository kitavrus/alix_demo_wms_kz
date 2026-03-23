<?php

use yii\db\Schema;
use yii\db\Migration;

class m151224_095354_change_field_plus_sum_minus_sum_table_bookkeeper extends Migration
{
    public function up()
    {
        $this->dropColumn('{{%bookkeeper}}', 'plus_sum');
        $this->dropColumn('{{%bookkeeper}}', 'minus_sum');
        $this->addColumn('{{%bookkeeper}}', 'price', Schema::TYPE_DECIMAL. '(26,3)  NULL DEFAULT 0 comment "приход и расход" AFTER  `balance_sum`');

    }

    public function down()
    {
        $this->dropColumn('{{%bookkeeper}}', 'price');
        $this->addColumn('{{%bookkeeper}}', 'plus_sum', Schema::TYPE_DECIMAL. '(26,3)  NULL DEFAULT 0 comment "приход" AFTER  `balance_sum`');
        $this->addColumn('{{%bookkeeper}}', 'minus_sum', Schema::TYPE_DECIMAL. '(26,3)  NULL DEFAULT 0 comment "расход" AFTER  `balance_sum`');
    }
}
