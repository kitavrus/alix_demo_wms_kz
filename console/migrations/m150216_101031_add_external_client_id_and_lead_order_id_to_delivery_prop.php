<?php

use yii\db\Schema;
use yii\db\Migration;

class m150216_101031_add_external_client_id_and_lead_order_id_to_delivery_prop extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','external_client_lead_id', Schema::TYPE_INTEGER . ' NULL AFTER `client_id`');
        $this->addColumn('{{%tl_delivery_proposals}}','transportation_order_lead_id', Schema::TYPE_INTEGER . ' NULL AFTER `external_client_lead_id`');
    }

    public function down()
    {
        echo "m150216_101031_add_external_client_id_and_lead_order_id_to_delivery_prop cannot be reverted.\n";

        return false;
    }
}
