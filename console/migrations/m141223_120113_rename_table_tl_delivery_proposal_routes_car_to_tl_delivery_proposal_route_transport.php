<?php

use yii\db\Schema;
use yii\db\Migration;

class m141223_120113_rename_table_tl_delivery_proposal_routes_car_to_tl_delivery_proposal_route_transport extends Migration
{
    public function up()
    {
        $this->execute('RENAME TABLE `tl_delivery_proposal_routes_car` TO `tl_delivery_proposal_route_transport`');



    }

    public function down()
    {

        $this->execute('RENAME TABLE `tl_delivery_proposal_route_transport` TO `tl_delivery_proposal_routes_car`');
        return false;
    }
}
