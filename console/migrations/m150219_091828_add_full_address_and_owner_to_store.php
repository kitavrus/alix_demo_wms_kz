<?php

use yii\db\Schema;
use yii\db\Migration;

class m150219_091828_add_full_address_and_owner_to_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','contact_full_name', Schema::TYPE_STRING . ' NULL AFTER `shopping_center_name`');
        $this->addColumn('{{%store}}','owner_type', Schema::TYPE_INTEGER . ' NULL AFTER `type_use`');
    }

    public function down()
    {
        echo "m150219_091828_add_full_address_and_owner_to_store cannot be reverted.\n";

        return false;
    }
}
