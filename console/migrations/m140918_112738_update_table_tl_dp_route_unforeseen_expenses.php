<?php

use yii\db\Schema;
use yii\db\Migration;

class m140918_112738_update_table_tl_dp_route_unforeseen_expenses extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%tl_delivery_proposal_route_unforeseen_expenses}}','price','price_cache');
        $this->renameColumn('{{%tl_delivery_proposal_route_unforeseen_expenses}}','with_vat','price_with_vat');
    }

    public function down()
    {
        echo "m140918_112738_update_table_tl_dp_route_unforeseen_expenses cannot be reverted.\n";

        return false;
    }
}
