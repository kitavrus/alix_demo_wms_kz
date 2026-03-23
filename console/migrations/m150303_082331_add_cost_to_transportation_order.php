<?php

use yii\db\Schema;
use yii\db\Migration;

class m150303_082331_add_cost_to_transportation_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%transportation_order_lead}}','cost', Schema::TYPE_DECIMAL . '(26,2)  NULL DEFAULT "0" comment "Pre cost of delivery" AFTER `declared_value`');
        $this->addColumn('{{%transportation_order_lead}}','cost_vat', Schema::TYPE_DECIMAL . '(26,2)  NULL DEFAULT "0" comment "Pre cost of delivery with vat" AFTER `cost`');
    }

    public function down()
    {
        $this->dropColumn('{{%transportation_order_lead}}','cost');
        $this->dropColumn('{{%transportation_order_lead}}','cost_vat');

        return true;
    }
}
