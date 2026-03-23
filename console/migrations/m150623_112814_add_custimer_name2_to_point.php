<?php

use yii\db\Schema;
use yii\db\Migration;

class m150623_112814_add_custimer_name2_to_point extends Migration
{
    public function up()
    {
        $this->addColumn('{{%transportation_order_lead}}','customer_phone_2',Schema::TYPE_STRING . '(128) NULL AFTER `customer_phone`');
    }

    public function down()
    {
        $this->dropColumn('{{%transportation_order_lead}}','customer_phone_2');
    }
}
