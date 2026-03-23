<?php

use yii\db\Migration;

/**
 * Class m191219_064327_ecommerce_add_client_shipment_source_to_outbound
 */
class m191219_064327_ecommerce_add_client_shipment_source_to_outbound extends Migration
{

    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound}}', 'client_ShipmentSource', $this->string()->defaultValue('')->comment("Shipment Source")->after('client_StoreName'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound}}', 'client_ShipmentSource');
        return false;
    }
}