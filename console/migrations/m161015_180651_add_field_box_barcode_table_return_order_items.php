<?php

use yii\db\Migration;

class m161015_180651_add_field_box_barcode_table_return_order_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%return_order_items}}','box_barcode',$this->string(64)->defaultValue('')->comment("Box barcode")->after('product_serialize_data'));
        $this->addColumn('{{%return_order_items}}','client_box_barcode',$this->string(64)->defaultValue('')->comment("Client box barcode")->after('box_barcode'));
    }

    public function down()
    {
        $this->dropColumn('{{%return_order_items}}','box_barcode');
        $this->dropColumn('{{%return_order_items}}','client_box_barcode');
        return false;
    }
}