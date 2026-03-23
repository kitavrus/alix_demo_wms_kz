<?php

use yii\db\Schema;
use yii\db\Migration;

class m140923_092928_add_fields_to_table_tl_delivery_proposal_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_orders}}','order_number',Schema::TYPE_STRING . '(128) NULL COMMENT "Order number" AFTER `order_id`');
        $this->addColumn('{{%tl_delivery_proposal_orders}}','number_places',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Number places" AFTER `order_number`');
        $this->addColumn('{{%tl_delivery_proposal_orders}}','number_places_actual',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Actual number places" AFTER `number_places`');

        $this->addColumn('{{%tl_delivery_proposal_orders}}','mc',Schema::TYPE_DECIMAL . '(26,3) NULL COMMENT "M3" AFTER `number_places`');
        $this->addColumn('{{%tl_delivery_proposal_orders}}','kg',Schema::TYPE_DECIMAL . '(26,3) NULL COMMENT "Kilogram" AFTER `mc`');

        $this->addColumn('{{%tl_delivery_proposal_orders}}','mc_actual',Schema::TYPE_DECIMAL . '(26,3) NULL COMMENT "Mc actual" AFTER `mc`');
        $this->addColumn('{{%tl_delivery_proposal_orders}}','kg_actual',Schema::TYPE_DECIMAL . '(26,3) NULL COMMENT "Kilogram actual" AFTER `kg`');

        $this->addColumn('{{%tl_delivery_proposal_orders}}','status',Schema::TYPE_SMALLINT . '(6) NULL COMMENT "Status: new, scanned, packed, etc..." AFTER `client_id`');

    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_orders}}','order_number');
        $this->dropColumn('{{%tl_delivery_proposal_orders}}','number_places');
        $this->dropColumn('{{%tl_delivery_proposal_orders}}','number_places_actual');
        $this->dropColumn('{{%tl_delivery_proposal_orders}}','mc');
        $this->dropColumn('{{%tl_delivery_proposal_orders}}','kg');
        $this->dropColumn('{{%tl_delivery_proposal_orders}}','mc_actual');
        $this->dropColumn('{{%tl_delivery_proposal_orders}}','kg_actual');
        $this->dropColumn('{{%tl_delivery_proposal_orders}}','status');
    }
}