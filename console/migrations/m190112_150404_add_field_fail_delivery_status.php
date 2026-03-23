<?php

use yii\db\Migration;

/**
 * Class m190112_150404_add_field_fail_delivery_status
 */
class m190112_150404_add_field_fail_delivery_status extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','fail_delivery_status',$this->text()->defaultValue('')->comment("Fail delivery status")->after('date_delivered'));
        $this->addColumn('{{%cross_dock}}','fail_delivery_status',$this->text()->defaultValue('')->comment("Fail delivery status")->after('date_delivered'));
        $this->addColumn('{{%tl_delivery_proposals}}','fail_delivery_status',$this->text()->defaultValue('')->comment("Fail delivery status")->after('status_invoice'));
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','fail_delivery_status');
        $this->dropColumn('{{%cross_dock}}','fail_delivery_status');
        $this->dropColumn('{{%tl_delivery_proposals}}','fail_delivery_status');
        return false;
    }
}