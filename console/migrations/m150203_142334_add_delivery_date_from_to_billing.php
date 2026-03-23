<?php

use yii\db\Schema;
use yii\db\Migration;

class m150203_142334_add_delivery_date_from_to_billing extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_billing}}','delivery_term_from',Schema::TYPE_INTEGER . ' DEFAULT NULL AFTER `delivery_term`');
        $this->addColumn('{{%tl_delivery_proposal_billing}}','delivery_term_to',Schema::TYPE_INTEGER . ' DEFAULT NULL AFTER `delivery_term_from`');
    }

    public function down()
    {
        echo "m150203_142334_add_delivery_date_from_to_billing.\n";

        return false;
    }
}
