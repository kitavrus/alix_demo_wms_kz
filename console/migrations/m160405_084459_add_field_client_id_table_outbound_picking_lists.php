<?php

use yii\db\Schema;
use yii\db\Migration;

class m160405_084459_add_field_client_id_table_outbound_picking_lists extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_picking_lists}}', 'client_id', Schema::TYPE_INTEGER. '(11) NULL DEFAULT "0" comment "client id" AFTER  `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_picking_lists}}', 'client_id');
    }
}