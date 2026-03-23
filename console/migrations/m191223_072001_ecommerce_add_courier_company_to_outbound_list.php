<?php

use yii\db\Migration;

/**
 * Class m191223_072001_ecommerce_add_сourier_сompany_to_outbound_list
 */
class m191223_072001_ecommerce_add_courier_company_to_outbound_list extends Migration
{

    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound_list}}', 'courier_company', $this->string()->defaultValue('')->comment("Courier company")->after('status'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound_list}}', 'courier_company');
        return false;
    }
}