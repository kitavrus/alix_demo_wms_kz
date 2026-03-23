<?php

use yii\db\Schema;
use yii\db\Migration;

class m150313_051654_add_fields_data_created_on_client_table_outbound_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','data_created_on_client', Schema::TYPE_STRING . '(64)  NULL DEFAULT "" comment "Date time created order on client" AFTER `end_datetime`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','data_created_on_client');
    }
}
