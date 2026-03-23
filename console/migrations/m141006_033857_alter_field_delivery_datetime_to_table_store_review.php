<?php

use yii\db\Schema;
use yii\db\Migration;

class m141006_033857_alter_field_delivery_datetime_to_table_store_review extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `store_reviews` CHANGE `delivery_datetime` `delivery_datetime` DATETIME NULL DEFAULT NULL COMMENT 'Delivered date time'");
    }

    public function down()
    {
        echo "m141006_033857_alter_field_delivery_datetime_to_table_store_review cannot be reverted.\n";

        return false;
    }
}
