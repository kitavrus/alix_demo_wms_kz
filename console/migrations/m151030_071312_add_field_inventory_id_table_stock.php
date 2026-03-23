<?php

use yii\db\Schema;
use yii\db\Migration;

class m151030_071312_add_field_inventory_id_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}', 'inventory_id', Schema::TYPE_INTEGER. '(11) NULL DEFAULT "0" comment "" AFTER  `status_lost`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}', 'inventory_id');
    }
}