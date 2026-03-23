<?php

use yii\db\Schema;
use yii\db\Migration;

class m150212_150623_change_order_number_field_in_order_lead extends Migration
{
    public function up()
    {
        $this->alterColumn('transportation_order_lead', 'order_number', Schema::TYPE_STRING);
    }

    public function down()
    {
        echo "m150212_150623_change_order_number_field_in_order_lead cannot be reverted.\n";

        return false;
    }
}
