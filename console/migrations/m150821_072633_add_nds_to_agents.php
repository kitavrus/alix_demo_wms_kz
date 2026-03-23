<?php

use yii\db\Schema;
use yii\db\Migration;

class m150821_072633_add_nds_to_agents extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_agents}}', 'flag_nds', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 comment "NDS flag" AFTER `status`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_agents}}', 'flag_nds');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}