<?php

use yii\db\Schema;
use yii\db\Migration;

class m150929_111659_add_field_on_stock_table_clients extends Migration
{
    public function up()
    {
        $this->addColumn('{{%clients}}', 'on_stock', Schema::TYPE_SMALLINT . ' NULL DEFAULT "0" comment "показывем на вмс и/или тмс" AFTER  `status`');
    }

    public function down()
    {
        $this->dropColumn('{{%clients}}', 'on_stock');
    }
}