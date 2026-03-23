<?php

use yii\db\Migration;

class m161102_065540_add_field_status_notification_table_consignment_universal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%consignment_universal}}','status_notification',
            $this->smallInteger()
                 ->defaultValue(0)
                 ->comment("Status notification")
                 ->after('end_datetime')
        );
    }

    public function down()
    {
        $this->dropColumn('{{%consignment_universal}}','status_notification');
        return false;
    }
}