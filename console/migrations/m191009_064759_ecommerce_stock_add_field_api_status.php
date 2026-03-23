<?php

use yii\db\Migration;

/**
 * Class m191009_064759_ecommerce_stock_add_field_api_status
 */
class m191009_064759_ecommerce_stock_add_field_api_status extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_stock}}','api_status',$this->smallInteger()->defaultValue(0)->comment("API status")->after('status_outbound'));
        $this->addColumn('{{%ecommerce_inbound_items}}','api_status',$this->smallInteger()->defaultValue(0)->comment("API status")->after('status'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_stock}}','api_status');
        $this->dropColumn('{{%ecommerce_inbound_items}}','api_status');
        return false;
    }
}
