<?php

use yii\db\Schema;
use yii\db\Migration;

class m141002_075019_add_fields_to_table_delivery_proposal_routes_car extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','order_number',Schema::TYPE_STRING . '(128) NULL COMMENT "Order number" AFTER `tl_delivery_proposal_route_cars_id`');
        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','order_id',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Order id" AFTER `order_number`');
        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','number_places',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Number places" AFTER `order_number`');
        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','number_places_actual',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Actual number places" AFTER `number_places`');

        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','mc',Schema::TYPE_DECIMAL . '(26,3) NULL COMMENT "M3" AFTER `number_places`');
        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','kg',Schema::TYPE_DECIMAL . '(26,3) NULL COMMENT "Kilogram" AFTER `mc`');

        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','mc_actual',Schema::TYPE_DECIMAL . '(26,3) NULL COMMENT "Mc actual" AFTER `mc`');
        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','kg_actual',Schema::TYPE_DECIMAL . '(26,3) NULL COMMENT "Kilogram actual" AFTER `kg`');

    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_routes_car}}','order_number');
        $this->dropColumn('{{%tl_delivery_proposal_routes_car}}','order_id');
        $this->dropColumn('{{%tl_delivery_proposal_routes_car}}','number_places');
        $this->dropColumn('{{%tl_delivery_proposal_routes_car}}','number_places_actual');
        $this->dropColumn('{{%tl_delivery_proposal_routes_car}}','mc');
        $this->dropColumn('{{%tl_delivery_proposal_routes_car}}','kg');
        $this->dropColumn('{{%tl_delivery_proposal_routes_car}}','mc_actual');
        $this->dropColumn('{{%tl_delivery_proposal_routes_car}}','kg_actual');
    }
}
