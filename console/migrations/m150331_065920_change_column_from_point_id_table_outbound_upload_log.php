<?php

use yii\db\Schema;
use yii\db\Migration;

class m150331_065920_change_column_from_point_id_table_outbound_upload_log extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `outbound_upload_log` CHANGE `to_point_id` `to_point_id` INT( 11 ) NULL DEFAULT '0' COMMENT 'Store (point) id'");
    }

    public function down()
    {
        echo "m150331_065920_change_column_from_point_id_table_outbound_upload_log cannot be reverted.\n";
        return false;
    }

}
