<?php

use yii\db\Migration;

/**
 * Class m191025_095455_ecommerce_outbound_add_field_path_to_order_doc
 */
class m191025_095455_ecommerce_outbound_add_field_path_to_order_doc extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound}}','path_to_order_doc',$this->string(512)->defaultValue('')->comment("path_to_order_doc")->after('path_to_cargo_label_file'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound}}','path_to_order_doc');
        return false;
    }
}