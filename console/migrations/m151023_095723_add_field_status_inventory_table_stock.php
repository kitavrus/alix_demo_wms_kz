<?php

use yii\db\Schema;
use yii\db\Migration;

class m151023_095723_add_field_status_inventory_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}', 'status_inventory', Schema::TYPE_SMALLINT. ' NULL DEFAULT "0" comment "" AFTER  `inventory_secondary_address`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}', 'status_inventory');
    }
}
