<?php

use yii\db\Schema;
use yii\db\Migration;

class m140924_180758_change_field_format_in_Tl_delivery_proposal_routes extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposal_routes` CHANGE `mc_actual` `mc_actual`".Schema::TYPE_DECIMAL . "(26,3) NULL");
    }

    public function down()
    {
        echo "m140924_180758_change_field_format_in_Tl_delivery_proposal_routes cannot be reverted.\n";

        return false;
    }
}
