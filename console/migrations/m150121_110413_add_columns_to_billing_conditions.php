<?php

use yii\db\Schema;
use yii\db\Migration;

class m150121_110413_add_columns_to_billing_conditions extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_billing_conditions}}','title',Schema::TYPE_STRING . ' DEFAULT NULL');
        $this->addColumn('{{%tl_delivery_proposal_billing_conditions}}','delivery_type',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_delivery_proposal_billing_conditions}}','sort_order',Schema::TYPE_SMALLINT . ' DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_billing_conditions}}','title');
        $this->dropColumn('{{%tl_delivery_proposal_billing_conditions}}','delivery_type');
        $this->dropColumn('{{%tl_delivery_proposal_billing_conditions}}','sort_order');
    }
}
