<?php

use yii\db\Migration;

/**
 * Class m200618_081321_ecommerce_return_items_add_product
 */
class m200618_081321_ecommerce_return_items_add_product extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_return_items}}', 'product_barcode1', $this->string(18)->defaultValue('')->comment("")->after('product_barcode'));
        $this->addColumn('{{%ecommerce_return_items}}', 'product_barcode2', $this->string(18)->defaultValue('')->comment("")->after('product_barcode1'));
        $this->addColumn('{{%ecommerce_return_items}}', 'product_barcode3', $this->string(18)->defaultValue('')->comment("")->after('product_barcode2'));
        $this->addColumn('{{%ecommerce_return_items}}', 'product_barcode4', $this->string(18)->defaultValue('')->comment("")->after('product_barcode3'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_return_items}}', 'product_barcode1');
        $this->dropColumn('{{%ecommerce_return_items}}', 'product_barcode2');
        $this->dropColumn('{{%ecommerce_return_items}}', 'product_barcode3');
        $this->dropColumn('{{%ecommerce_return_items}}', 'product_barcode4');
        return false;
    }
}
