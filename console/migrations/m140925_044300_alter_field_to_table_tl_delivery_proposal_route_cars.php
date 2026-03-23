<?php

use yii\db\Schema;
use yii\db\Migration;

class m140925_044300_alter_field_to_table_tl_delivery_proposal_route_cars extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposal_route_cars` CHANGE `price_invoice` `price_invoice`".Schema::TYPE_DECIMAL . "(26,3) NULL DEFAULT 0");
    }

    public function down()
    {
        echo "m140925_044300_alter_field_to_table_tl_delivery_proposal_route_cars cannot be reverted.\n";

        return false;
    }
}
