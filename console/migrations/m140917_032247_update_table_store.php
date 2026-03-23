<?php

use yii\db\Schema;
use yii\db\Migration;

class m140917_032247_update_table_store extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%store}}','country','country_id');
        $this->alterColumn('{{%store}}','country_id',Schema::TYPE_INTEGER . ' NULL COMMENT "Country" AFTER `id`');

        $this->renameColumn('{{%store}}','region','region_id');
        $this->alterColumn('{{%store}}','region_id',Schema::TYPE_INTEGER . ' NULL COMMENT "Region" AFTER `country_id`');


//        $this->renameColumn('{{%warehouse}}','city','city_id');
//        $this->alterColumn('{{%warehouse}}','city_id',Schema::TYPE_INTEGER . ' NULL COMMENT "City" AFTER `region_id`');
    }

    public function down()
    {
        echo "m140917_032247_update_table_store cannot be reverted.\n";

        return false;
    }
}
