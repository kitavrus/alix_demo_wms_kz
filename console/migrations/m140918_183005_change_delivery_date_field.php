<?php

use yii\db\Schema;
use yii\db\Migration;

class m140918_183005_change_delivery_date_field extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposals` CHANGE `delivery_date` `delivery_date` DATETIME NULL");
        $this->execute("ALTER TABLE `tl_delivery_proposal_route_cars` CHANGE `delivery_date` `delivery_date` DATETIME NULL");
        $this->execute("ALTER TABLE `tl_delivery_proposal_route_unforeseen_expenses` CHANGE `delivery_date` `delivery_date` DATETIME NULL");
        $this->execute("ALTER TABLE `tl_delivery_proposal_routes` CHANGE `delivery_date` `delivery_date` DATETIME NULL");
        $this->execute("ALTER TABLE `tl_order` CHANGE `delivery_date` `delivery_date` DATETIME NULL");
    }

    public function down()
    {
        echo "m140918_183005_change_delivery_date_field cannot be reverted.\n";

        return false;
    }
}
