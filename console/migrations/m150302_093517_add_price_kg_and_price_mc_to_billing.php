<?php

use yii\db\Schema;
use yii\db\Migration;

class m150302_093517_add_price_kg_and_price_mc_to_billing extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_billing}}','price_invoice_kg', Schema::TYPE_DECIMAL . '(26,2)  NULL DEFAULT "0" comment "Price by kg" AFTER `price_invoice_with_vat`');
        $this->addColumn('{{%tl_delivery_proposal_billing}}','price_invoice_kg_with_vat', Schema::TYPE_DECIMAL . '(26,2)  NULL DEFAULT "0" comment "Price by kg with vat" AFTER `price_invoice_kg`');
        $this->addColumn('{{%tl_delivery_proposal_billing}}','price_invoice_mc', Schema::TYPE_DECIMAL . '(26,2)  NULL DEFAULT "0" comment "Price by mc" AFTER `price_invoice_kg_with_vat`');
        $this->addColumn('{{%tl_delivery_proposal_billing}}','price_invoice_mc_with_vat', Schema::TYPE_DECIMAL . '(26,2)  NULL DEFAULT "0" comment "Price by mc with vat" AFTER `price_invoice_mc`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_billing}}','price_invoice_kg');
        $this->dropColumn('{{%tl_delivery_proposal_billing}}','price_invoice_kg_with_vat');
        $this->dropColumn('{{%tl_delivery_proposal_billing}}','price_invoice_mc');
        $this->dropColumn('{{%tl_delivery_proposal_billing}}','price_invoice_mc_with_vat');
        return true;
    }
}
