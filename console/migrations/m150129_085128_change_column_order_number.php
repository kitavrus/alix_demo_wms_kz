<?php

use yii\db\Schema;
use yii\db\Migration;

class m150129_085128_change_column_order_number extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `transportation_order_lead` CHANGE `order_number` `order_number` varchar(128) NULL DEFAULT NULL");
    }

    public function down()
    {
        echo "m150129_085128_change_column_order_number cannot be reverted.\n";

        return false;
    }
}
