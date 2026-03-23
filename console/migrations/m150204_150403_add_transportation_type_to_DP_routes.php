<?php

use yii\db\Schema;
use yii\db\Migration;

class m150204_150403_add_transportation_type_to_DP_routes extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_routes}}','transportation_type',Schema::TYPE_INTEGER . ' NULL COMMENT  "Type of transportation" AFTER `route_to`');
    }

    public function down()
    {
        echo "m150204_150403_add_transportation_type_to_DP_routes cannot be reverted.\n";

        return false;
    }
}
