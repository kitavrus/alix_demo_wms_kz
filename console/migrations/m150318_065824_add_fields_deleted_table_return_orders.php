<?php

use yii\db\Schema;
use yii\db\Migration;

class m150318_065824_add_fields_deleted_table_return_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%return_order_items}}','deleted', Schema::TYPE_SMALLINT .' NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('{{%return_order_items}}','deleted');
    }
}
