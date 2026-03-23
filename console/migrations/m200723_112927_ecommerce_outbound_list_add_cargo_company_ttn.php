<?php

use yii\db\Migration;

/**
 * Class m200723_112927_ecommerce_outbound_list_add_cargo_company_ttn
 */
class m200723_112927_ecommerce_outbound_list_add_cargo_company_ttn extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound_list}}', 'cargo_company_ttn', $this->string(36)->defaultValue('')->comment("")->after('courier_company'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound_list}}', 'cargo_company_ttn');
        return false;
    }
}
