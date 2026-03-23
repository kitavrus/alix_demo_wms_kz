<?php

use yii\db\Schema;
use yii\db\Migration;

class m141010_094750_alter_field_to_date_order_accepted_date_accepted_order_table_all_route extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','shipped_datetime',Schema::TYPE_DATETIME . ' NULL COMMENT "Date of order shipped" AFTER `delivery_date`');
        $this->addColumn('{{%tl_delivery_proposals}}','accepted_datetime',Schema::TYPE_DATETIME . ' NULL COMMENT "Date of order accepted" AFTER `shipped_datetime`');

        $this->addColumn('{{%tl_delivery_proposal_routes}}','shipped_datetime',Schema::TYPE_DATETIME . ' NULL COMMENT "Date of order shipped" AFTER `delivery_date`');
        $this->addColumn('{{%tl_delivery_proposal_routes}}','accepted_datetime',Schema::TYPE_DATETIME . ' NULL COMMENT "Date of order accepted" AFTER `shipped_datetime`');

        $this->addColumn('{{%tl_delivery_proposal_route_cars}}','shipped_datetime',Schema::TYPE_DATETIME . ' NULL COMMENT "Date of order shipped" AFTER `delivery_date`');
        $this->addColumn('{{%tl_delivery_proposal_route_cars}}','accepted_datetime',Schema::TYPE_DATETIME . ' NULL COMMENT "Date of order accepted" AFTER `shipped_datetime`');

    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','accepted_datetime');
        $this->dropColumn('{{%tl_delivery_proposals}}','shipped_datetime');

        $this->dropColumn('{{%tl_delivery_proposal_routes}}','accepted_datetime');
        $this->dropColumn('{{%tl_delivery_proposal_routes}}','shipped_datetime');

        $this->dropColumn('{{%tl_delivery_proposal_route_cars}}','accepted_datetime');
        $this->dropColumn('{{%tl_delivery_proposal_route_cars}}','shipped_datetime');

    }
}
