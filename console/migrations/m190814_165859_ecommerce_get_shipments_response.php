<?php

use yii\db\Migration;

/**
 * Class m190814_165859_ecommerce_get_shipments_response
 */
class m190814_165859_ecommerce_get_shipments_response extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_get_shipments_response', [
            'id' => $this->primaryKey(),
            'get_shipments_request_id' => $this->integer(11)->defaultValue(0)->comment(''),
            'ShipmentId' => $this->string(64)->defaultValue('')->comment(''),
            'ExternalShipmentNo' => $this->string(64)->defaultValue('')->comment(''),
            'ShipmentType' => $this->string(512)->defaultValue('')->comment(''),
            'ShipmentSource' => $this->string(512)->defaultValue('')->comment(''),
            'ShipmentDate' => $this->string(512)->defaultValue('')->comment(''),
            'Priority' => $this->string(64)->defaultValue('')->comment(''),
            'CustomerName' => $this->string(512)->defaultValue('')->comment(''),
            'ShippingAddress' => $this->string(512)->defaultValue('')->comment(''),
            'ShippingCountryCode' => $this->string(64)->defaultValue('')->comment(''),
            'ShippingCity' => $this->string(512)->defaultValue('')->comment(''),
            'ShippingCounty' => $this->string(512)->defaultValue('')->comment(''),
            'ShippingZipCode' => $this->string(64)->defaultValue('')->comment(''),
            'ShippingEmail' => $this->string(512)->defaultValue('')->comment(''),
            'ShippingPhone' => $this->string(512)->defaultValue('')->comment(''),
            'Destination' => $this->string(512)->defaultValue('')->comment(''),
            'CourierCompany' => $this->string(512)->defaultValue('')->comment(''),
            'FromBusinessUnitId' => $this->string(64)->defaultValue('')->comment(''),
            'CacStoreID' => $this->string(64)->defaultValue('')->comment(''),
            'PartyApprovalId' => $this->string(64)->defaultValue('')->comment(''),
            'PackMessage' => $this->string(512)->defaultValue('')->comment(''),
            'IsGiftWrapping' => $this->string(64)->defaultValue('')->comment(''),
            'GiftWrappingMessage' => $this->string(512)->defaultValue('')->comment(''),
            'Ek1' => $this->string(64)->defaultValue('')->comment(''),
            'Ek2' => $this->string(64)->defaultValue('')->comment(''),
            'Ek3' => $this->string(64)->defaultValue('')->comment(''),
            'B2CShipmentDetailList' => $this->string(64)->defaultValue('')->comment(''),

            'error_message' => $this->text()->defaultValue('')->comment(''),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_get_shipments_response}}');
    }
}
