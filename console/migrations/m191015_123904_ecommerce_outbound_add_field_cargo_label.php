<?php

use yii\db\Migration;

/**
 * Class m191015_123904_ecommerce_outbound_add_field_cargo_label
 */
class m191015_123904_ecommerce_outbound_add_field_cargo_label extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound}}','path_to_cargo_label_file',$this->string(512)->defaultValue('')->comment("Path to cargo label")->after('date_delivered_to_customer'));
        $this->addColumn('{{%ecommerce_outbound}}','client_TrackingNumber',$this->string()->defaultValue('')->comment("Cargo company TrackingNumber")->after('path_to_cargo_label_file'));
        $this->addColumn('{{%ecommerce_outbound}}','client_TrackingUrl',$this->string()->defaultValue('')->comment("Cargo company TrackingUrl")->after('client_TrackingNumber'));
        $this->addColumn('{{%ecommerce_outbound}}','client_ReferenceNumber',$this->string()->defaultValue('')->comment("Cargo company ReferenceNumber")->after('client_TrackingUrl'));
        $this->addColumn('{{%ecommerce_outbound}}','client_CancelReason',$this->string(1024)->defaultValue('')->comment("Cargo company ReferenceNumber")->after('client_ReferenceNumber'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound}}','path_to_cargo_label_file');
        $this->dropColumn('{{%ecommerce_outbound}}','client_TrackingNumber');
        $this->dropColumn('{{%ecommerce_outbound}}','client_TrackingUrl');
        $this->dropColumn('{{%ecommerce_outbound}}','client_ReferenceNumber');
        $this->dropColumn('{{%ecommerce_outbound}}','client_CancelReason');
        return false;
    }
}