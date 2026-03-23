<?php

use yii\db\Schema;
use yii\db\Migration;

class m150327_043854_alter_field_order_number_to_table_return_orders extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%return_orders}}','order_number',Schema::TYPE_STRING . '(28) DEFAULT ""');
    }

    public function down()
    {
        $this->alterColumn('{{%return_orders}}','order_number',Schema::TYPE_INTEGER . '(11)');

        return false;
    }
}
