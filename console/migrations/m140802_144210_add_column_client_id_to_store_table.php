<?php

use yii\db\Schema;
use yii\db\Migration;

class m140802_144210_add_column_client_id_to_store_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','client_id',Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Internal client id" AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','client_id');
    }
}
