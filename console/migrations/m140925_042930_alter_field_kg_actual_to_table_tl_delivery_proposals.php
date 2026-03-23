<?php

use yii\db\Schema;
use yii\db\Migration;

class m140925_042930_alter_field_kg_actual_to_table_tl_delivery_proposals extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposals` CHANGE `kg_actual` `kg_actual`".Schema::TYPE_DECIMAL . "(26,3) NULL default 0");
    }

    public function down()
    {
        echo "m140925_042930_alter_field_kg_actual_to_table_tl_delivery_proposals cannot be reverted.\n";

        return false;
    }
}
