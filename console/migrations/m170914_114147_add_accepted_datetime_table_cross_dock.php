<?php

use yii\db\Migration;

class m170914_114147_add_accepted_datetime_table_cross_dock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock}}','accepted_datetime',$this->integer()->defaultValue(null)->comment("Accepted dateTime")->after('end_datetime'));
    }

    public function down()
    {
        $this->dropColumn('{{%cross_dock}}','accepted_datetime');
        return false;
    }
}