<?php

use yii\db\Schema;
use yii\db\Migration;

class m150804_074712_add_ready_to_invoicing_to_dp extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','ready_to_invoicing',Schema::TYPE_SMALLINT . ' DEFAULT 0 AFTER `is_client_confirmed`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','ready_to_invoicing');
    }

}