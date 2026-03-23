<?php

use yii\db\Schema;
use yii\db\Migration;

class m150120_095540_add_field_base_type_table_codebook extends Migration
{
    public function up()
    {
        $this->addColumn('{{%codebook}}','base_type',Schema::TYPE_SMALLINT . ' NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('{{%codebook}}','base_type');
    }
}
