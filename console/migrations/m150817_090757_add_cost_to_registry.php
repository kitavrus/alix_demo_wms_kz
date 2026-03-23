<?php

use yii\db\Schema;
use yii\db\Migration;

class m150817_090757_add_cost_to_registry extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_outbound_registry}}', 'price_invoice', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT 0 comment "car price"');
        $this->addColumn('{{%tl_outbound_registry}}', 'price_invoice_with_vat', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT 0 comment "car price"');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_outbound_registry}}', 'price_invoice');
        $this->dropColumn('{{%tl_outbound_registry}}', 'price_invoice_with_vat');
    }
}