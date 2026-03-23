<?php

use yii\db\Migration;

/**
 * Class m191015_150319_ecommerce_outbound_add_field_api_status
 */
class m191015_150319_ecommerce_outbound_add_field_api_status extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound}}','api_status',$this->smallInteger()->defaultValue(0)->comment("API status")->after('status'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound}}','api_status');
        return false;
    }
}