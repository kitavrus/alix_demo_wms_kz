<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_080127_add_currency_to_customs_accounts extends Migration
{
    public function up()
    {
        $this->addColumn('{{%customs_accounts}}', 'currency', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 comment "Валюта" AFTER  `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%customs_accounts}}', 'currency');

    }

}
