<?php

use yii\db\Migration;
use yii\db\Schema;

class m251221_220606_tmp_index extends Migration
{
    public function up()
    {

		//$this->createIndex('client_id', 'product_barcodes', 'client_id');
		//$this->createIndex('barcode', 'product_barcodes', 'barcode');

//        $this->dropIndex(
//            'client_id',
//            'product'
//        );
//        $this->dropIndex(
//            'client_product_id',
//            'product'
//        );
//        $this->dropIndex(
//            'barcode',
//            'product'
//        );
//        $this->dropIndex(
//            'model',
//            'product'
//        );

		$this->createIndex('client_id', 'product', 'client_id');
		$this->createIndex('barcode', 'product', 'barcode');
		$this->createIndex('client_product_id', 'product', 'client_product_id');
		$this->createIndex('model', 'product', 'model');

    }

    public function down()
    {
    }
}