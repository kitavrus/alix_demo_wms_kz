<?php

use yii\db\Schema;
use yii\db\Migration;

class m141209_103137_add_fields_price_price_with_vat_to_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','car_price_invoice', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" COMMENT "Car price" AFTER `driver_auto_number`');
        $this->addColumn('{{%tl_delivery_proposals}}','car_price_invoice_with_vat', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" COMMENT "Car price with NDS" AFTER `car_price_invoice`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','car_price_invoice');
        $this->dropColumn('{{%tl_delivery_proposals}}','car_price_invoice_with_vat');
    }
}
