<?php

use yii\db\Schema;
use yii\db\Migration;

class m150401_082659_add_column_order_type_table_inbound_upload_log extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_upload_log}}','order_type',Schema::TYPE_SMALLINT . '(1) NULL DEFAULT "0" COMMENT "Type: from stock, cross-dock" AFTER `expected_qty`');
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_upload_log}}','order_type');
    }
}
