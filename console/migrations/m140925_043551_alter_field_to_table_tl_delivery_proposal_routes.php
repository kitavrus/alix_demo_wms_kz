<?php

use yii\db\Schema;
use yii\db\Migration;

class m140925_043551_alter_field_to_table_tl_delivery_proposal_routes extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposal_routes` CHANGE `kg` `kg`".Schema::TYPE_DECIMAL . "(26,3) NULL DEFAULT 0");
        $this->execute("ALTER TABLE `tl_delivery_proposal_routes` CHANGE `kg_actual` `kg_actual`".Schema::TYPE_DECIMAL . "(26,3) NULL DEFAULT 0");
        $this->execute("ALTER TABLE `tl_delivery_proposal_routes` CHANGE `price_invoice` `price_invoice`".Schema::TYPE_DECIMAL . "(26,3) NULL DEFAULT 0");
    }

    public function down()
    {
        echo "m140925_043551_alter_field_to_table_tl_delivery_proposal_routes cannot be reverted.\n";

        return false;
    }
}
