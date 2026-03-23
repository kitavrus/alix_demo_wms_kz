<?php

use yii\db\Migration;

/**
 * Class m191024_103055_ecommerce_outbound_add_field_total_price
 */
class m191024_103055_ecommerce_outbound_add_field_total_price extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound}}','total_price',$this->string(36)->defaultValue('')->comment("Total price")->after('place_accepted_qty'));
        $this->addColumn('{{%ecommerce_outbound}}','total_price_tax',$this->string(36)->defaultValue('')->comment("Total price tax")->after('total_price'));
        $this->addColumn('{{%ecommerce_outbound}}','total_price_discount',$this->string(36)->defaultValue('')->comment("Total price tax")->after('total_price'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound}}','total_price');
        $this->dropColumn('{{%ecommerce_outbound}}','total_price_tax');
        $this->dropColumn('{{%ecommerce_outbound}}','total_price_discount');
        return false;
    }
}