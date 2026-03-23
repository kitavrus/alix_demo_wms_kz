<?php

use yii\db\Schema;
use yii\db\Migration;

class m150619_062719_change_column_consignment_inbound_id_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','consignment_inbound_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Consignment inbound id" AFTER `inbound_order_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','consignment_inbound_id');
    }
}
