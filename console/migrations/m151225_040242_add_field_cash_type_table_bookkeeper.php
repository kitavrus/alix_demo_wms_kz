<?php

use yii\db\Schema;
use yii\db\Migration;

class m151225_040242_add_field_cash_type_table_bookkeeper extends Migration
{
    public function up()
    {
        $this->addColumn('{{%bookkeeper}}', 'expenses_type_id', Schema::TYPE_SMALLINT. ' NULL DEFAULT "1" comment "" AFTER  `balance_sum`');
        $this->addColumn('{{%bookkeeper}}', 'cash_type', Schema::TYPE_SMALLINT. ' NULL DEFAULT "1" comment "" AFTER  `balance_sum`');
        $this->addColumn('{{%bookkeeper}}', 'client_id', Schema::TYPE_INTEGER. '(11) NULL DEFAULT "1" comment "" AFTER  `balance_sum`');
    }

    public function down()
    {
        $this->dropColumn('{{%bookkeeper}}', 'expenses_type_id');
        $this->dropColumn('{{%bookkeeper}}', 'cash_type');
        $this->dropColumn('{{%bookkeeper}}', 'client_id');
    }
}
