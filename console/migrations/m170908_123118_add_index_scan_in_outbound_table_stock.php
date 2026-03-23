<?php

use yii\db\Migration;

class m170908_123118_add_index_scan_in_outbound_table_stock extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `stock`
ADD INDEX `scan_in_datetime` (`scan_in_datetime`),
ADD INDEX `scan_out_datetime` (`scan_out_datetime`);");
    }

    public function down()
    {
        echo "m170908_123118_add_index_scan_in_outbound_table_stock cannot be reverted.\n";

        return false;
    }

}
