<?php

use yii\db\Schema;
use yii\db\Migration;

class m150513_092128_add_column_internal_code_count_table_clients extends Migration
{
    public function up()
    {
        $this->addColumn('{{%clients}}','internal_code_count', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Internal count code" AFTER `status`');
    }

    public function down()
    {
        $this->dropColumn('{{%clients}}','internal_code_count');
    }
}
