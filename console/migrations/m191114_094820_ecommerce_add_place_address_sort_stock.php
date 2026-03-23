<?php

use yii\db\Migration;

/**
 * Class m191114_094820_ecommerce_add_place_address_sort_stock
 */
class m191114_094820_ecommerce_add_place_address_sort_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_stock}}','place_address_sort1',$this->integer()->defaultValue(0)->comment("Для сортировки 1")->after('place_address_barcode'));
        $this->addColumn('{{%ecommerce_stock}}','place_address_sort2',$this->integer()->defaultValue(0)->comment("Для сортировки 2")->after('place_address_sort1'));
        $this->addColumn('{{%ecommerce_stock}}','place_address_sort3',$this->integer()->defaultValue(0)->comment("Для сортировки 3")->after('place_address_sort2'));
        $this->addColumn('{{%ecommerce_stock}}','place_address_sort4',$this->integer()->defaultValue(0)->comment("Для сортировки 4")->after('place_address_sort3'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_stock}}','place_address_sort1');
        $this->dropColumn('{{%ecommerce_stock}}','place_address_sort2');
        $this->dropColumn('{{%ecommerce_stock}}','place_address_sort3');
        $this->dropColumn('{{%ecommerce_stock}}','place_address_sort4');
        return false;
    }
}