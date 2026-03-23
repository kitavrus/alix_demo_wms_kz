<?php

use yii\db\Schema;
use yii\db\Migration;

class m140917_061222_add_column_type_use_table_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','type_use',Schema::TYPE_SMALLINT . ' NULL COMMENT "Type: store, stock, etc.." AFTER `client_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','type_use');
    }
}
