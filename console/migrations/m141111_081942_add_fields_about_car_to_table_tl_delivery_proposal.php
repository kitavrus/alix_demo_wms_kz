<?php

use yii\db\Schema;
use yii\db\Migration;

class m141111_081942_add_fields_about_car_to_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','driver_name',Schema::TYPE_STRING . '(128) NULL DEFAULT "" comment "Driver name" AFTER `company_transporter`');
        $this->addColumn('{{%tl_delivery_proposals}}','driver_phone',Schema::TYPE_STRING . '(128) NULL DEFAULT "" comment "Driver phone" AFTER `driver_name`');
        $this->addColumn('{{%tl_delivery_proposals}}','driver_auto_number',Schema::TYPE_STRING . '(64) NULL DEFAULT "" comment "Driver auto number" AFTER `driver_phone`');
    }

    public function down()
    {
        echo "m141111_081942_add_fields_about_car_to_table_tl_delivery_proposal cannot be reverted.\n";

        return false;
    }
}
