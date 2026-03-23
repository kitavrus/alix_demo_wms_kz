<?php

use yii\db\Schema;
use yii\db\Migration;

class m141107_093254_add_field_company_transporter_to_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','company_transporter',Schema::TYPE_SMALLINT . ' NULL DEFAULT "0" comment "Nomadex, RLC" AFTER `route_to`');
    }

    public function down()
    {
        echo "m141107_093254_add_field_company_transporter_to_table_tl_delivery_proposal cannot be reverted.\n";

        return false;
    }
}
