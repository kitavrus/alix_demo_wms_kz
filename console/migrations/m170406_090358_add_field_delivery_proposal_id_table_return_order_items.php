<?php

use yii\db\Migration;

class m170406_090358_add_field_delivery_proposal_id_table_return_order_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%return_order_items}}','delivery_proposal_id',$this->integer()->defaultValue(0)->comment("TTN")->after('return_order_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%return_order_items}}','delivery_proposal_id');
        return false;
    }
}