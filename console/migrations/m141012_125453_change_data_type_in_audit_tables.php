<?php

use yii\db\Schema;
use yii\db\Migration;

class m141012_125453_change_data_type_in_audit_tables extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposals_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
        $this->execute("ALTER TABLE `tl_delivery_proposal_routes_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
        $this->execute("ALTER TABLE `tl_delivery_proposal_routes_car_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
        $this->execute("ALTER TABLE `tl_delivery_proposal_route_cars_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
        $this->execute("ALTER TABLE `tl_delivery_proposal_route_unforeseen_expenses_audit` CHANGE `date_created` `date_created`".Schema::TYPE_DATETIME . " NULL");
    }


    public function down()
    {
        echo "m141012_125453_change_data_type_in_audit_tables cannot be reverted.\n";

        return false;
    }
}
