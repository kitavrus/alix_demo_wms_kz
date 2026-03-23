<?php

use yii\db\Schema;
use yii\db\Migration;

class m141218_041725_add_field_change_mckgnp_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','change_mckgnp', Schema::TYPE_INTEGER . ' NULL DEFAULT "1" COMMENT "Change mc, kg, np  If not empty" AFTER `company_transporter`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','change_mckgnp');
    }
}
