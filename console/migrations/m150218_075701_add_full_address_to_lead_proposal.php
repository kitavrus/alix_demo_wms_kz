<?php

use yii\db\Schema;
use yii\db\Migration;

class m150218_075701_add_full_address_to_lead_proposal extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%transportation_order_lead}}', 'customer_address', 'customer_street');
        $this->addColumn('{{%transportation_order_lead}}','customer_house', Schema::TYPE_STRING . ' NULL AFTER `customer_street`');
        $this->addColumn('{{%transportation_order_lead}}','customer_apartment', Schema::TYPE_STRING . ' NULL AFTER `customer_house`');
        $this->addColumn('{{%transportation_order_lead}}','customer_floor', Schema::TYPE_STRING . ' NULL AFTER `customer_apartment`');

        $this->renameColumn('{{%transportation_order_lead}}', 'recipient_address', 'recipient_street');
        $this->addColumn('{{%transportation_order_lead}}','recipient_house', Schema::TYPE_STRING . ' NULL AFTER `recipient_street`');
        $this->addColumn('{{%transportation_order_lead}}','recipient_apartment', Schema::TYPE_STRING . ' NULL AFTER `recipient_house`');
        $this->addColumn('{{%transportation_order_lead}}','recipient_floor', Schema::TYPE_STRING . ' NULL AFTER `recipient_apartment`');

    }

    public function down()
    {
        echo "m150218_075701_add_full_address_to_lead_proposal cannot be reverted.\n";

        return false;
    }
}
