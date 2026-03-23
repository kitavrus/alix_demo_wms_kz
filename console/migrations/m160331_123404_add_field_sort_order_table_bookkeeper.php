<?php

use yii\db\Schema;
use yii\db\Migration;

class m160331_123404_add_field_sort_order_table_bookkeeper extends Migration
{
    public function up()
    {
        $this->addColumn('{{%bookkeeper}}', 'sort_order', Schema::TYPE_INTEGER. '(11) NULL DEFAULT 0 comment "for sorting" AFTER  `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%bookkeeper}}', 'sort_order');
    }
}
