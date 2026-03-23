<?php

use yii\db\Schema;
use yii\db\Migration;

class m150202_041253_add_fields_barcode_table_employees extends Migration
{
    public function up()
    {
        $this->addColumn('{{%employees}}','barcode',Schema::TYPE_STRING . '(32) NULL DEFAULT "" COMMENT  "Barcode" AFTER `last_name`');
    }

    public function down()
    {
        $this->dropColumn('{{%employees}}','barcode');

        return false;
    }
}
