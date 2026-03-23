<?php

use yii\db\Schema;
use yii\db\Migration;

class m150918_090317_add_field_inventory_primary_address_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}', 'inventory_primary_address', Schema::TYPE_STRING . '(25) NULL DEFAULT "" comment "старый шк короба" AFTER  `status_lost`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}', 'inventory_primary_address');
    }
}