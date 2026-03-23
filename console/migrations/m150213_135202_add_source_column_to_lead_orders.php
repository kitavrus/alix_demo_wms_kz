<?php

use yii\db\Schema;
use yii\db\Migration;

class m150213_135202_add_source_column_to_lead_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%transportation_order_lead}}','source', Schema::TYPE_INTEGER . ' NULL AFTER `status`');
    }

    public function down()
    {
        echo "m150213_135202_add_source_column_to_lead_orders cannot be reverted.\n";

        return false;
    }
}
