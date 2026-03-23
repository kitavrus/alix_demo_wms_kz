<?php

use yii\db\Migration;

class m160922_064242_add_field_product_serialize_data_table_cross_dock_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock_items}}','product_serialize_data',$this->text()->defaultValue('')->comment("Product serialize data")->after('accepted_number_places_qty'));
    }

    public function down()
    {
        $this->dropColumn('{{%cross_dock_items}}','product_serialize_data');
        return false;
    }
}