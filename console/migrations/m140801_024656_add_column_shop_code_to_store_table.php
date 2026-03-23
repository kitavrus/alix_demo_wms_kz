<?php

use yii\db\Schema;
use yii\db\Migration;

class m140801_024656_add_column_shop_code_to_store_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','shop_code',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "External store code" AFTER `comment`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','shop_code');
    }
}
