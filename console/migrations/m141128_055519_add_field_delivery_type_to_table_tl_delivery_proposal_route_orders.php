<?php

use yii\db\Schema;
use yii\db\Migration;

class m141128_055519_add_field_delivery_type_to_table_tl_delivery_proposal_route_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_route_orders}}','delivery_type',Schema::TYPE_SMALLINT . '(2) DEFAULT 0 COMMENT "Type: RPT or Cross-dock" AFTER `order_type`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_route_orders}}','delivery_type');
    }
}
