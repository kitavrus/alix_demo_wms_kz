<?php

use yii\db\Migration;

class m161102_065554_add_field_status_notification_table_consignment_universal_orders extends Migration
{

    public function up()
    {
        $this->addColumn('{{%consignment_universal_orders}}','status_notification',
            $this->smallInteger()
                 ->defaultValue(0)
                 ->comment("Status notification")
                 ->after('field_extra5')
        );
    }

    public function down()
    {
        $this->dropColumn('{{%consignment_universal_orders}}','status_notification');
        return false;
    }
}