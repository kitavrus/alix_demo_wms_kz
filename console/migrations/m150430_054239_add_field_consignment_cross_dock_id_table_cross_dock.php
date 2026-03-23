<?php

use yii\db\Schema;
use yii\db\Migration;

class m150430_054239_add_field_consignment_cross_dock_id_table_cross_dock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock}}','consignment_cross_dock_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Consignment cross dock id" AFTER `client_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%cross_dock}}','consignment_cross_dock_id');
    }
}
