<?php

use yii\db\Schema;
use yii\db\Migration;

class m150518_082127_add_cargo_status_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_orders}}','cargo_status',Schema::TYPE_SMALLINT . ' DEFAULT 0 AFTER `status`');
        $this->addColumn('{{%outbound_orders}}','cargo_status',Schema::TYPE_SMALLINT . ' DEFAULT 0 AFTER `status`');
        $this->addColumn('{{%cross_dock}}','cargo_status',Schema::TYPE_SMALLINT . ' DEFAULT 0 AFTER `status`');
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_orders}}','cargo_status');
        $this->dropColumn('{{%outbound_orders}}','cargo_status');
        $this->dropColumn('{{%cross_dock}}','cargo_status');
    }
}
