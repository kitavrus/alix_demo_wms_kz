<?php

use yii\db\Schema;
use yii\db\Migration;

class m150401_082139_add_column_order_type_table_outbound_upload_log extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_upload_log}}','order_type',Schema::TYPE_SMALLINT . '(1) NULL DEFAULT "0" COMMENT "Type: from stock, cross-dock" AFTER `from_point_title`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_upload_log}}','order_type');
    }
}
