<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_get_shipments_response".
 *
 * @property int $id
 * @property int $get_shipments_request_id
 * @property string $ShipmentId
 * @property string $ExternalShipmentNo
 * @property string $ShipmentType
 * @property string $ShipmentSource
 * @property string $ShipmentDate
 * @property string $Priority
 * @property string $CustomerName
 * @property string $ShippingAddress
 * @property string $ShippingCountryCode
 * @property string $ShippingCity
 * @property string $ShippingCounty
 * @property string $ShippingZipCode
 * @property string $ShippingEmail
 * @property string $ShippingPhone
 * @property string $Destination
 * @property string $CourierCompany
 * @property string $FromBusinessUnitId
 * @property string $CacStoreID
 * @property string $PartyApprovalId
 * @property string $PackMessage
 * @property string $IsGiftWrapping
 * @property string $GiftWrappingMessage
 * @property string $Ek1
 * @property string $Ek2
 * @property string $Ek3
 * @property string $B2CShipmentDetailList
 * @property string $error_message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceGetShipmentsResponse extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_get_shipments_response';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['get_shipments_request_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['ShipmentId', 'ExternalShipmentNo', 'ShipmentType', 'ShipmentSource', 'ShipmentDate', 'Priority', 'CustomerName', 'ShippingAddress', 'ShippingCountryCode', 'ShippingCity', 'ShippingCounty', 'ShippingZipCode', 'ShippingEmail', 'ShippingPhone', 'Destination', 'CourierCompany', 'FromBusinessUnitId', 'CacStoreID', 'PartyApprovalId', 'PackMessage', 'IsGiftWrapping', 'GiftWrappingMessage', 'Ek1', 'Ek2', 'Ek3', 'B2CShipmentDetailList'], 'string', 'max' => 512],
            [['error_message',], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'get_shipments_request_id' => Yii::t('app', 'Get Shipments Request ID'),
            'ShipmentId' => Yii::t('app', 'Shipment ID'),
            'ExternalShipmentNo' => Yii::t('app', 'External Shipment No'),
            'ShipmentType' => Yii::t('app', 'Shipment Type'),
            'ShipmentSource' => Yii::t('app', 'Shipment Source'),
            'ShipmentDate' => Yii::t('app', 'Shipment Date'),
            'Priority' => Yii::t('app', 'Priority'),
            'CustomerName' => Yii::t('app', 'Customer Name'),
            'ShippingAddress' => Yii::t('app', 'Shipping Address'),
            'ShippingCountryCode' => Yii::t('app', 'Shipping Country Code'),
            'ShippingCity' => Yii::t('app', 'Shipping City'),
            'ShippingCounty' => Yii::t('app', 'Shipping County'),
            'ShippingZipCode' => Yii::t('app', 'Shipping Zip Code'),
            'ShippingEmail' => Yii::t('app', 'Shipping Email'),
            'ShippingPhone' => Yii::t('app', 'Shipping Phone'),
            'Destination' => Yii::t('app', 'Destination'),
            'CourierCompany' => Yii::t('app', 'Courier Company'),
            'FromBusinessUnitId' => Yii::t('app', 'From Business Unit ID'),
            'CacStoreID' => Yii::t('app', 'Cac Store ID'),
            'PartyApprovalId' => Yii::t('app', 'Party Approval ID'),
            'PackMessage' => Yii::t('app', 'Pack Message'),
            'IsGiftWrapping' => Yii::t('app', 'Is Gift Wrapping'),
            'GiftWrappingMessage' => Yii::t('app', 'Gift Wrapping Message'),
            'Ek1' => Yii::t('app', 'Ek1'),
            'Ek2' => Yii::t('app', 'Ek2'),
            'Ek3' => Yii::t('app', 'Ek3'),
            'B2CShipmentDetailList' => Yii::t('app', 'B2 Cshipment Detail List'),
            'error_message' => Yii::t('app', 'Error message'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
