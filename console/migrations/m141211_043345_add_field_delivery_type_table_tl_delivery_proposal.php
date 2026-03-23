<?php

use yii\db\Schema;
use yii\db\Migration;

class m141211_043345_add_field_delivery_type_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','delivery_type', Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT "Type: Transfer, Simple" AFTER `company_transporter`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','delivery_type');
    }
}
