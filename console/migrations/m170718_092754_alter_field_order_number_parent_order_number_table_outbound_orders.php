<?php

use yii\db\Migration;

class m170718_092754_alter_field_order_number_parent_order_number_table_outbound_orders extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%outbound_orders}}','order_number','string');
        $this->alterColumn('{{%outbound_orders}}','parent_order_number','string');
    }

    public function down()
    {
        echo "m170718_092754_alter_field_order_number_parent_order_number_table_outbound_orders cannot be reverted.\n";

        return false;
    }
}