<?php

use yii\db\Schema;
use yii\db\Migration;

class m151023_095713_add_field_inventory_secondary_address_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}', 'inventory_secondary_address', Schema::TYPE_STRING . '(24) NULL DEFAULT "" comment "" AFTER  `inventory_primary_address`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}', 'inventory_secondary_address');
    }
}