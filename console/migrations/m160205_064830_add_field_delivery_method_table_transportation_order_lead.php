<?php

use yii\db\Schema;
use yii\db\Migration;

class m160205_064830_add_field_delivery_method_table_transportation_order_lead extends Migration
{
    public function up()
    {
        $this->addColumn('{{%transportation_order_lead}}', 'delivery_method', Schema::TYPE_SMALLINT. ' NULL DEFAULT NULL comment "" AFTER  `delivery_type`');
    }

    public function down()
    {
        $this->dropColumn('{{%transportation_order_lead}}', 'delivery_method');
    }
}
