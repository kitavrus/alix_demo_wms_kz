<?php

use yii\db\Schema;
use yii\db\Migration;

class m151209_052550_add_field_type_id_table_tl_delivery_proposal_route_unforeseen_expenses extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_route_unforeseen_expenses}}', 'type_id', Schema::TYPE_INTEGER. '(11) NULL DEFAULT "0" comment "Type unforeseen expenses id" AFTER  `tl_delivery_route_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_route_unforeseen_expenses}}', 'type_id');
    }
}