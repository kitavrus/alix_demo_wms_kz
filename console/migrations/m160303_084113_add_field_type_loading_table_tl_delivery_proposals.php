<?php

use yii\db\Schema;
use yii\db\Migration;

class m160303_084113_add_field_type_loading_table_tl_delivery_proposals extends Migration
{
    public function up()
    {
//        $this->addColumn('{{%tl_delivery_proposals}}', 'sender_contact', Schema::TYPE_STRING. '(512) NULL DEFAULT "" comment "" AFTER  `route_to`');
        $this->addColumn('{{%tl_delivery_proposals}}', 'transport_type_loading', Schema::TYPE_INTEGER. '(11) NULL DEFAULT 0 comment "Метод погрузки" AFTER  `transportation_order_lead_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}', 'transport_type_loading');
    }
}
