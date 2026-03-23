<?php

use yii\db\Schema;
use yii\db\Migration;

class m141119_100702_add_field_legal_point_name_to_table_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','legal_point_name',Schema::TYPE_STRING . '(128) NULL COMMENT "Legal point name" AFTER `name`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','legal_point_name');
    }
}
