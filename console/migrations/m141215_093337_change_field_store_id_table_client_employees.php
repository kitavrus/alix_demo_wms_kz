<?php

use yii\db\Schema;
use yii\db\Migration;

class m141215_093337_change_field_store_id_table_client_employees extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `client_employees` CHANGE `store_id` `store_id` INT( 11 ) NULL DEFAULT '0' COMMENT 'Store ID'");
    }

    public function down()
    {
        echo "m141215_093337_change_field_store_id_table_client_employees cannot be reverted.\n";

        return false;
    }
}
