<?php

use yii\db\Schema;
use yii\db\Migration;

class m140916_103131_change_city_to_city_id_in_table_store extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%store}}', 'city', 'city_id');
    }

    public function down()
    {
        echo "m140909_124758_rename_table_transport_logistics_order_to_tl_order_table cannot be reverted.\n";

        return false;
    }
}
