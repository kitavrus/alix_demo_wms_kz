<?php

use yii\db\Schema;
use yii\db\Migration;

class m151223_061845_add_field_type_id_table_bookkeeper extends Migration
{
    public function up()
    {
        $this->addColumn('{{%bookkeeper}}', 'type_id', Schema::TYPE_SMALLINT. ' NULL DEFAULT "1" comment "" AFTER  `balance_sum`');
    }

    public function down()
    {
        $this->dropColumn('{{%bookkeeper}}', 'type_id');
    }
}