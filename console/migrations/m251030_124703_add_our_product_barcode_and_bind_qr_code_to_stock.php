<?php

use yii\db\Migration;
use yii\db\Schema;

class m251030_124703_add_our_product_barcode_and_bind_qr_code_to_stock extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%stock}}',
            'our_product_barcode',
            Schema::TYPE_STRING . '(24) COMMENT "Our product barcode" AFTER `product_barcode`'
        );
        
        $this->addColumn(
            '{{%stock}}',
            'bind_qr_code',
            Schema::TYPE_STRING . '(512) COMMENT "Bind QR code" AFTER `our_product_barcode`'
        );
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}', 'our_product_barcode');
        $this->dropColumn('{{%stock}}', 'bind_qr_code');
    }
}