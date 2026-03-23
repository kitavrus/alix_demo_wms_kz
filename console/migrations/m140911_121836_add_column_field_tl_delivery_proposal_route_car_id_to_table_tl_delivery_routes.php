<?php

use yii\db\Schema;
use yii\db\Migration;

class m140911_121836_add_column_field_tl_delivery_proposal_route_car_id_to_table_tl_delivery_routes extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_routes}}','tl_delivery_proposal_route_car_id',Schema::TYPE_INTEGER . ' NULL COMMENT "" AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_routes}}','tl_delivery_proposal_route_car_id');
    }
}