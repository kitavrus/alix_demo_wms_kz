<?php

use yii\db\Migration;

/**
 * Class m191029_133620_ecommerce_outbound_package_type
 */
class m191029_133620_ecommerce_outbound_package_type extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound}}','package_type',$this->string(3)->defaultValue('')->comment("Package type")->after('packing_date'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound}}','package_type');
        return false;
    }
}