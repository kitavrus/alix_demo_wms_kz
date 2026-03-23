<?php

use yii\db\Schema;
use yii\db\Migration;

class m141013_052737_add_field_to_table_tl_delivery_proposal_routes_car extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','tl_delivery_proposal_id',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Delivery proposal id" AFTER `tl_delivery_proposal_route_cars_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_routes_car}}','tl_delivery_proposal_id');
    }
}
