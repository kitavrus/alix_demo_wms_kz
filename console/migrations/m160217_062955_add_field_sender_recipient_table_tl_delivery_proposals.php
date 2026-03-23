<?php

use yii\db\Schema;
use yii\db\Migration;

class m160217_062955_add_field_sender_recipient_table_tl_delivery_proposals extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}', 'sender_contact', Schema::TYPE_STRING. '(512) NULL DEFAULT "" comment "" AFTER  `route_to`');
        $this->addColumn('{{%tl_delivery_proposals}}', 'sender_contact_id', Schema::TYPE_INTEGER. '(11) NULL DEFAULT 0 comment "" AFTER  `sender_contact`');
        $this->addColumn('{{%tl_delivery_proposals}}', 'recipient_contact', Schema::TYPE_STRING. '(512) NULL DEFAULT "" comment "" AFTER  `sender_contact_id`');
        $this->addColumn('{{%tl_delivery_proposals}}', 'recipient_contact_id', Schema::TYPE_INTEGER. '(11) NULL DEFAULT 0 comment "" AFTER  `recipient_contact`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}', 'sender_contact');
        $this->dropColumn('{{%tl_delivery_proposals}}', 'sender_contact_id');
        $this->dropColumn('{{%tl_delivery_proposals}}', 'recipient_contact');
        $this->dropColumn('{{%tl_delivery_proposals}}', 'recipient_contact_id');
    }
}