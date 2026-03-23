<?php

use yii\db\Migration;

/**
 * Class m191024_075340_ecommerce_outbound_items_add_field_price
 */
class m191024_075340_ecommerce_outbound_items_add_field_price extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `ecommerce_outbound_items`CHANGE `product_price` `product_price` varchar(24) COLLATE 'utf8_general_ci' NULL DEFAULT '0' COMMENT 'Unit Product price' AFTER `end_datetime`;");

        $this->addColumn('{{%ecommerce_outbound_items}}','price_tax',$this->string(24)->defaultValue('0')->comment("Price Unit Tax")->after('product_price'));
        $this->addColumn('{{%ecommerce_outbound_items}}','price_discount',$this->string(24)->defaultValue('0')->comment("Price Unit Discount")->after('price_tax'));
        $this->addColumn('{{%ecommerce_outbound_items}}','comment_message',$this->string(512)->defaultValue('')->comment("Comment message")->after('status'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound_items}}','price_tax');
        $this->dropColumn('{{%ecommerce_outbound_items}}','price_discount');
        $this->dropColumn('{{%ecommerce_outbound_items}}','comment_message');
        return false;
    }
}