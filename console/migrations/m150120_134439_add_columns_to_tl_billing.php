<?php

use yii\db\Schema;
use yii\db\Migration;

class m150120_134439_add_columns_to_tl_billing extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_billing}}','delivery_term',Schema::TYPE_STRING . ' DEFAULT NULL AFTER `status`');
        $this->addColumn('{{%tl_delivery_proposal_billing}}','tariff_type',Schema::TYPE_SMALLINT . ' DEFAULT 0  COMMENT  "default etc" AFTER `delivery_term`');
        $this->addColumn('{{%tl_delivery_proposal_billing}}','cooperation_type',Schema::TYPE_SMALLINT . ' DEFAULT 0 COMMENT  "one-time, full freight etc" AFTER `tariff_type`');
        $this->addColumn('{{%tl_delivery_proposal_billing}}','delivery_type',Schema::TYPE_SMALLINT . ' DEFAULT 0 COMMENT "warhouse-warhouse, door-door etc" AFTER `cooperation_type`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_billing}}','delivery_term');
        $this->dropColumn('{{%tl_delivery_proposal_billing}}','tariff_type');
        $this->dropColumn('{{%tl_delivery_proposal_billing}}','cooperation_type');
        $this->dropColumn('{{%tl_delivery_proposal_billing}}','delivery_type');
    }
}
