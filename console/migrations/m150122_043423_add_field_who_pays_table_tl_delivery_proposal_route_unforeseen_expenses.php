<?php

use yii\db\Schema;
use yii\db\Migration;

class m150122_043423_add_field_who_pays_table_tl_delivery_proposal_route_unforeseen_expenses extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_route_unforeseen_expenses}}','who_pays',Schema::TYPE_SMALLINT . ' NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_route_unforeseen_expenses}}','who_pays');

        return false;
    }
}
