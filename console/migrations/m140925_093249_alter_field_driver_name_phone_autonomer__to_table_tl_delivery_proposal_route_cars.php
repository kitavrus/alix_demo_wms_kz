<?php

use yii\db\Schema;
use yii\db\Migration;

class m140925_093249_alter_field_driver_name_phone_autonomer__to_table_tl_delivery_proposal_route_cars extends Migration
{
    public function up()
    {

        $this->addColumn('{{%tl_delivery_proposal_route_cars}}','driver_name',Schema::TYPE_STRING . '(128) NULL COMMENT "Driver name" AFTER `delivery_date`');
        $this->addColumn('{{%tl_delivery_proposal_route_cars}}','driver_phone',Schema::TYPE_STRING . '(128) NULL COMMENT "Driver phone" AFTER `driver_name`');
        $this->addColumn('{{%tl_delivery_proposal_route_cars}}','driver_auto_number',Schema::TYPE_STRING . '(64) NULL COMMENT "Auto number" AFTER `driver_phone`');

    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_route_cars}}','driver_name');
        $this->dropColumn('{{%tl_delivery_proposal_route_cars}}','driver_phone');
        $this->dropColumn('{{%tl_delivery_proposal_route_cars}}','driver_auto_number');
    }
}
