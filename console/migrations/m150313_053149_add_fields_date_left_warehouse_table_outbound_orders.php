<?php

use yii\db\Schema;
use yii\db\Migration;

class m150313_053149_add_fields_date_left_warehouse_table_outbound_orders extends Migration
{

    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','date_left_warehouse', Schema::TYPE_STRING . '(64)  NULL DEFAULT "" comment "Date left from our warehouse" AFTER `data_created_on_client`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','date_left_warehouse');
    }

}
