<?php

use yii\db\Schema;
use yii\db\Migration;

class m140922_045735_alter_field_grzch_to_table_tl_delivery_proposal_route_cars extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposal_route_cars` CHANGE `grzch` `grzch` INT( 11 ) NULL DEFAULT '0' COMMENT 'Грзч'");
    }

    public function down()
    {
        echo "m140922_045735_alter_field_grzch_to_table_tl_delivery_proposal_route_cars cannot be reverted.\n";

        return false;
    }
}
