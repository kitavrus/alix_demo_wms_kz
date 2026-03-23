<?php

use yii\db\Schema;
use yii\db\Migration;

class m141111_081704_change_field_route_from_route_from_to_table_tl_delivery_proposal_billing extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposal_billing` CHANGE `route_from` `route_from` INT( 11 ) NULL DEFAULT '0' COMMENT 'Example: DC-APORT'");
        $this->execute("ALTER TABLE `tl_delivery_proposal_billing` CHANGE `route_to` `route_to` INT( 11 ) NULL DEFAULT '0' COMMENT 'Example: DC-APORT'");
    }

    public function down()
    {
        echo "m141111_081704_change_field_route_from_route_from_to_table_tl_delivery_proposal_billing cannot be reverted.\n";

        return false;
    }
}
