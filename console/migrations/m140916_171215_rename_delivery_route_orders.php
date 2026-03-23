<?php

use yii\db\Schema;
use yii\db\Migration;

class m140916_171215_rename_delivery_route_orders extends Migration
{
    public function up()
    {
        $this->execute("RENAME TABLE `tl_delivery_route_orders` TO `tl_delivery_proposal_route_orders`;");
    }

    public function down()
    {
        $this->execute("RENAME TABLE `tl_delivery_proposal_route_orders` TO `tl_delivery_route_orders`;");
    }
}
