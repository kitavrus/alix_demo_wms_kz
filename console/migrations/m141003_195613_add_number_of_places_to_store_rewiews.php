<?php

use yii\db\Schema;
use yii\db\Migration;

class m141003_195613_add_number_of_places_to_store_rewiews extends Migration
{

    public function up()
    {
        $this->addColumn('{{%store_reviews}}','number_of_places',Schema::TYPE_SMALLINT . ' NULL AFTER `delivery_datetime`');
    }

    public function down()
    {
        $this->dropColumn('{{%store_reviews}}','number_of_places');
    }
}
