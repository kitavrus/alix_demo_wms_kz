<?php

use yii\db\Schema;
use yii\db\Migration;

class m140917_032238_update_table_warehouse extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%warehouse}}','country','country_id');
        $this->alterColumn('{{%warehouse}}','country_id',Schema::TYPE_INTEGER . ' NULL COMMENT "Country" AFTER `id`');

        $this->renameColumn('{{%warehouse}}','region','region_id');
        $this->alterColumn('{{%warehouse}}','region_id',Schema::TYPE_INTEGER . ' NULL COMMENT "Region" AFTER `country_id`');


        $this->renameColumn('{{%warehouse}}','city','city_id');
        $this->alterColumn('{{%warehouse}}','city_id',Schema::TYPE_INTEGER . ' NULL COMMENT "City" AFTER `region_id`');
    }

    public function down()
    {
        echo "m140917_032238_update_table_warehouse cannot be reverted.\n";

        return false;
    }
}
