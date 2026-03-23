<?php

use yii\db\Schema;
use yii\db\Migration;

class m140824_231246_add_column_status_to_client_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%client}}','status',Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT "0" COMMENT "Status active no active" AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%client}}','status');
    }
}
