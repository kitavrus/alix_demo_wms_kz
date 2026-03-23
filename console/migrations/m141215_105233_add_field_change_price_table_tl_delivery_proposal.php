<?php

use yii\db\Schema;
use yii\db\Migration;

class m141215_105233_add_field_change_price_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','change_price', Schema::TYPE_INTEGER . ' NULL DEFAULT "1" COMMENT "Change price If price not empty" AFTER `company_transporter`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','change_price');
    }
}
