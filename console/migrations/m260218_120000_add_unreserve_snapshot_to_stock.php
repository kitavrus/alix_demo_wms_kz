<?php

use yii\db\Migration;
use yii\db\Schema;

class m260218_120000_add_unreserve_snapshot_to_stock extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%stock}}',
            'unreserve_snapshot',
            Schema::TYPE_TEXT . ' NULL COMMENT "Snapshot data for unreserve operation" AFTER `field_extra5`'
        );
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}', 'unreserve_snapshot');
    }
}

