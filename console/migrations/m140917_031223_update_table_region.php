<?php

use yii\db\Schema;
use yii\db\Migration;

class m140917_031223_update_table_region extends Migration
{
    public function up()
    {
        $this->addColumn('{{%region}}','country_id',Schema::TYPE_INTEGER . ' NULL COMMENT "Country" AFTER `id`');
    }

    public function down()
    {
        echo "m140917_031223_update_table_region cannot be reverted.\n";

        return false;
    }
}
