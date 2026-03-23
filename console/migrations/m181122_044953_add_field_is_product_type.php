<?php

use yii\db\Migration;

/**
 * Class m181122_044953_add_field_is_product_type
 */
class m181122_044953_add_field_is_product_type extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','is_product_type',$this->integer(11)->defaultValue(0)->comment("Product type return or one lot box")->after('product_sku'));
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','is_product_type');
        return false;
    }
}
