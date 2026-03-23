<?php

use yii\db\Schema;
use yii\db\Migration;

class m141104_081139_alter_field_price_invoice_to_table_tl_delivery_proposals extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposals` CHANGE `price_invoice` `price_invoice` DECIMAL( 26, 3 ) NULL DEFAULT '0' COMMENT 'Sale for client'");
    }

    public function down()
    {
        echo "m141104_081139_alter_field_price_invoice_to_table_tl_delivery_proposals cannot be reverted.\n";

        return false;
    }
}
