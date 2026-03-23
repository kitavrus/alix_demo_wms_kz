<?php

use yii\db\Schema;
use yii\db\Migration;

class m140802_144222_add_column_client_id_to_order_process_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order_process}}','client_id',Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Internal client id" AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%order_process}}','client_id');
    }
}
