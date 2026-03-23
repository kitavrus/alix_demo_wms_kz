<?php

use yii\db\Schema;
use yii\db\Migration;

class m150417_064950_add_fields_delivery_type_table_consignment_in_out_upload_log extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_upload_log}}','delivery_type', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 COMMENT "CROSS-DOCK, RPT, etc ... " AFTER `order_type`');
        $this->addColumn('{{%outbound_upload_log}}','delivery_type', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 COMMENT "CROSS-DOCK, RPT, etc ... " AFTER `order_type`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_upload_log}}','delivery_type');
        $this->dropColumn('{{%inbound_upload_log}}','delivery_type');
    }
}
