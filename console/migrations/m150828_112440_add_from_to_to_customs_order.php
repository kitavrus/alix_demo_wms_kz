<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_112440_add_from_to_to_customs_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%customs_orders}}', 'from', Schema::TYPE_STRING . ' NULL DEFAULT "" comment "Откуда" AFTER  `customs_accounts_id`');
        $this->addColumn('{{%customs_orders}}', 'to', Schema::TYPE_STRING . ' NULL DEFAULT "" comment "Куда" AFTER  `from`');
    }

    public function down()
    {
        $this->dropColumn('{{%customs_orders}}', 'from');
        $this->dropColumn('{{%customs_orders}}', 'to');
    }

}
