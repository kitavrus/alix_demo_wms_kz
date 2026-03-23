<?php

use yii\db\Schema;
use yii\db\Migration;

class m150313_134740_add_packing_date_to_outbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','packing_date', Schema::TYPE_STRING . '(64)  NULL DEFAULT "" comment "Date of end order package" AFTER `data_created_on_client`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','packing_date');
    }
}
