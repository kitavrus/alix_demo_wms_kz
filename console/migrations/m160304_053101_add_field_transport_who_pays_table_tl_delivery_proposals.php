<?php

use yii\db\Schema;
use yii\db\Migration;

class m160304_053101_add_field_transport_who_pays_table_tl_delivery_proposals extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}', 'transport_who_pays', Schema::TYPE_SMALLINT. '(2) NULL DEFAULT 0 comment "Кто платит" AFTER  `transport_type_loading`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}', 'transport_who_pays');
    }
}