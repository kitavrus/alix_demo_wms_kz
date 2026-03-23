<?php

use yii\db\Schema;
use yii\db\Migration;

class m150313_053229_add_fields_date_delivered_table_outbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','date_delivered', Schema::TYPE_STRING . '(64)  NULL DEFAULT "" comment "Date delivered to point" AFTER `date_left_warehouse`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','date_delivered');
    }
}
