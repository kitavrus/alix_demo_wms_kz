<?php

use yii\db\Schema;
use yii\db\Migration;

class m140916_165728_rename_Tl_delivery_routes_table extends Migration
{
    public function up()
    {
        $this->execute("RENAME TABLE `tl_delivery_routes` TO `tl_delivery_proposal_routes`;");
    }

    public function down()
    {
        $this->execute("RENAME TABLE `tl_delivery_proposal_routes` TO `tl_delivery_routes`;");
    }
}
