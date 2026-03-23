<?php

use yii\db\Schema;
use yii\db\Migration;

class m150311_143502_add_seal_to_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','seal', Schema::TYPE_STRING . ' NULL comment "Plomba" AFTER `company_transporter`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','seal');

        return true;
    }
}
