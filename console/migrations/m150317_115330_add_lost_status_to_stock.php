<?php

use yii\db\Schema;
use yii\db\Migration;

class m150317_115330_add_lost_status_to_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','status_lost', Schema::TYPE_INTEGER .' NULL DEFAULT "0" comment "Lost status" AFTER `status_availability`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','status_lost');

    }
}
