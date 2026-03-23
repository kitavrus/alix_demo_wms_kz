<?php

use yii\db\Schema;
use yii\db\Migration;

class m150604_062841_add_date_column_to_cross_dock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock}}','date_left_warehouse',Schema::TYPE_INTEGER . '(11) AFTER `end_datetime`');
        $this->addColumn('{{%cross_dock}}','date_delivered',Schema::TYPE_INTEGER . '(11) AFTER `date_left_warehouse`');

    }

    public function down()
    {
        $this->dropColumn('{{%cross_dock}}','date_left_warehouse');
        $this->dropColumn('{{%cross_dock}}','date_delivered');

    }
}
