<?php

use yii\db\Migration;

/**
 * Class m191108_070738_ecommerce_outbound_add_field_note_message
 */
class m191108_070738_ecommerce_outbound_add_field_note_message extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `ecommerce_stock` ADD `system_message` varchar(256) COLLATE 'utf8_general_ci' NULL COMMENT 'системный комментарий' AFTER `address_sort_order`, ADD `note_message1` varchar(256) COLLATE 'utf8_general_ci' NULL COMMENT 'Мои заметки' AFTER `system_message`, ADD `note_message2` varchar(256) COLLATE 'utf8_general_ci' NULL COMMENT 'Мои заметки' AFTER `note_message1`");
    }

    public function down()
    {
        return false;
    }
}