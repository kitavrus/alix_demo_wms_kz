<?php

use yii\db\Schema;
use yii\db\Migration;

class m150225_091201_add_delivery_type_to_order_lead extends Migration
{
    public function up()
    {
        $this->addColumn('{{%transportation_order_lead}}','delivery_type', Schema::TYPE_INTEGER . ' NULL AFTER `client_id`');
    }

    public function down()
    {
        echo "m150225_091201_add_delivery_type_to_order_lead cannot be reverted.\n";

        return false;
    }
}
