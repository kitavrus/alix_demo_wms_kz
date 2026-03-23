<?php

use yii\db\Schema;
use yii\db\Migration;

class m150921_080202_add_field_volumetric_weight_table_tl_delivery_proposals extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}', 'volumetric_weight', Schema::TYPE_DECIMAL . '(26,3) NULL DEFAULT "0" comment "объемный вес" AFTER  `kg_actual`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}', 'volumetric_weight');
    }
}
