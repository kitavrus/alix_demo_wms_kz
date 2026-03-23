<?php

use yii\db\Schema;
use yii\db\Migration;

class m150205_040942_add_fields_client_id_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','client_id',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Client id" AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','client_id');

        return false;
    }
}
