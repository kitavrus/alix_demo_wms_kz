<?php

use yii\db\Schema;
use yii\db\Migration;

class m140918_060431_update_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','price_expenses_total',Schema::TYPE_DECIMAL . '(26,3)  NULL COMMENT "Price expenses" AFTER `price_invoice_with_vat`'); // Расходы общие
        $this->addColumn('{{%tl_delivery_proposals}}','price_expenses_cache',Schema::TYPE_DECIMAL . '(26,3)  NULL COMMENT "Price expenses cache " AFTER `price_expenses_total`'); // Расходы наличные
        $this->addColumn('{{%tl_delivery_proposals}}','price_expenses_with_vat',Schema::TYPE_DECIMAL . '(26,3)  NULL COMMENT "Price expenses with vat" AFTER `price_expenses_cache`');// Расходы с ндс
        $this->addColumn('{{%tl_delivery_proposals}}','price_our_profit',Schema::TYPE_DECIMAL . '(26,3)  NULL COMMENT "Our price" AFTER `price_expenses_with_vat`');//  Наша прибыль

    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','price_expenses_total');
        $this->dropColumn('{{%tl_delivery_proposals}}','price_expenses_cache');
        $this->dropColumn('{{%tl_delivery_proposals}}','price_expenses_with_vat');
        $this->dropColumn('{{%tl_delivery_proposals}}','price_our_profit');
    }
}
