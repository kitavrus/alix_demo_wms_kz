<?php

use yii\db\Migration;

class m161004_114714_add_field_product_serialize_data_table_return_order_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%return_order_items}}','product_serialize_data',$this->text()->defaultValue('')->comment("Product serialize data")->after('accepted_qty'));
    }

    public function down()
    {
        $this->dropColumn('{{%return_order_items}}','product_serialize_data');
        return false;
    }
}