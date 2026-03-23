<?php

use yii\db\Schema;
use yii\db\Migration;

class m150320_093250_add_fields_city_lat_table_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','city_lat', Schema::TYPE_STRING . ' NULL comment "City name on roman alphabet" AFTER `city_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','city_lat');

        return true;
    }
}
