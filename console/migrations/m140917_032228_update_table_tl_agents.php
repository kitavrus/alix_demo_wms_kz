<?php

use yii\db\Schema;
use yii\db\Migration;

class m140917_032228_update_table_tl_agents extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%tl_agents}}','country','country_id');
        $this->alterColumn('{{%tl_agents}}','country_id',Schema::TYPE_INTEGER . ' NULL COMMENT "Country" AFTER `id`');

        $this->renameColumn('{{%tl_agents}}','region','region_id');
        $this->alterColumn('{{%tl_agents}}','region_id',Schema::TYPE_INTEGER . ' NULL COMMENT "Region" AFTER `country_id`');


        $this->renameColumn('{{%tl_agents}}','city','city_id');
        $this->alterColumn('{{%tl_agents}}','city_id',Schema::TYPE_INTEGER . ' NULL COMMENT "City" AFTER `region_id`');
    }

    public function down()
    {
        echo "m140917_032228_update_table_tl_agents cannot be reverted.\n";

        return false;
    }
}
