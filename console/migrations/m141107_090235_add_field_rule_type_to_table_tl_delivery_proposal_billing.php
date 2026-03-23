<?php

use yii\db\Schema;
use yii\db\Migration;

class m141107_090235_add_field_rule_type_to_table_tl_delivery_proposal_billing extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_billing}}','rule_type',Schema::TYPE_SMALLINT . ' NULL DEFAULT "0" comment "By mc, kg, condition" AFTER `route_to`');
    }

    public function down()
    {
        echo "m141107_090235_add_field_rule_type_to_table_tl_delivery_proposal_billing cannot be reverted.\n";

        return false;
    }
}
