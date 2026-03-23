<?php

use yii\db\Schema;
use yii\db\Migration;

class m141111_092556_add_fields_car_id_agent_id_to_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','car_id',Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" comment "Internal car id" AFTER `company_transporter`');
        $this->addColumn('{{%tl_delivery_proposals}}','agent_id',Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" comment "Internal agent id" AFTER `car_id`');
    }

    public function down()
    {
        echo "m141111_092556_add_fields_car_id_agent_id_to_table_tl_delivery_proposal cannot be reverted.\n";

        return false;
    }
}
