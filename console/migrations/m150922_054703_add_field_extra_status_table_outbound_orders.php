<?php

use yii\db\Schema;
use yii\db\Migration;

class m150922_054703_add_field_extra_status_table_outbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}', 'extra_status', Schema::TYPE_STRING . '(256) NULL DEFAULT "" comment "Специальный статус" AFTER  `cargo_status`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}', 'extra_status');
    }
}