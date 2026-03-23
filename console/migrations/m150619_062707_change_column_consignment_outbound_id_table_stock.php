<?php

use yii\db\Schema;
use yii\db\Migration;

class m150619_062707_change_column_consignment_outbound_id_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','consignment_outbound_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Consignment outbound id" AFTER `outbound_order_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','consignment_outbound_id');
    }
}
