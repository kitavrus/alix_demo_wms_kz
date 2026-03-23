<?php

use yii\db\Schema;
use yii\db\Migration;

class m140925_034802_alter_field_routeto_routefrom_to_table_tl_delivery_proposal_route_cars extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposal_route_cars` CHANGE `route_from` `route_city_from` INT( 11 ) NULL DEFAULT NULL COMMENT 'Example: Astana', CHANGE `route_to` `route_city_to` INT( 11 ) NULL DEFAULT NULL COMMENT 'Example: Astana'");
    }

    public function down()
    {
        echo "m140925_034802_alter_field_routeto_routefrom_to_table_tl_delivery_proposal_route_cars cannot be reverted.\n";

        return false;
    }
}
