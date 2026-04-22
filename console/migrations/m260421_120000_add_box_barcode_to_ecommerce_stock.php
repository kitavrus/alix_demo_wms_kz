<?php

use yii\db\Migration;

class m260421_120000_add_box_barcode_to_ecommerce_stock extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%ecommerce_stock}}',
            'box_barcode',
            $this->string(54)->null()->comment('Штрих-код коробки упаковки (pick/pack flow)')
        );
        $this->createIndex(
            'idx_ecommerce_stock_box_barcode',
            '{{%ecommerce_stock}}',
            'box_barcode'
        );
    }

    public function down()
    {
        $this->dropIndex('idx_ecommerce_stock_box_barcode', '{{%ecommerce_stock}}');
        $this->dropColumn('{{%ecommerce_stock}}', 'box_barcode');
        return true;
    }
}
