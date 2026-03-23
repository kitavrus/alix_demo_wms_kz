<?php

use yii\db\Schema;
use yii\db\Migration;

class m150310_075203_add_shopping_center_name_lat extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','shopping_center_name_lat', Schema::TYPE_STRING . ' NULL comment "Shop center name on roman alphabet" AFTER `shopping_center_name`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','shopping_center_name_lat');

        return true;
    }
}
