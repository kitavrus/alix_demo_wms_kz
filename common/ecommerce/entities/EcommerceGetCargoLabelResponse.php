<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_get_cargo_label_response".
 *
 * @property int $id
 * @property int $cargo_label_request_id
 * @property int $our_outbound_id
 * @property string $ExternalShipmentId
 * @property string $ShipmentId
 * @property string $FileExtension
 * @property string $FileData
 * @property string $TrackingNumber
 * @property string $TrackingUrl
 * @property string $ReferenceNumber
 * @property string $PageSize
 * @property string $error_message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceGetCargoLabelResponse extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_get_cargo_label_response';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cargo_label_request_id','our_outbound_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['FileData', 'error_message'], 'string'],
            [['ExternalShipmentId', 'ShipmentId', 'FileExtension', 'TrackingNumber', 'TrackingUrl', 'ReferenceNumber', 'PageSize'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cargo_label_request_id' => 'cargo label request id',
            'our_outbound_id' => 'Our Outbound ID',
            'ExternalShipmentId' => 'External Shipment ID',
            'ShipmentId' => 'Shipment ID',
            'FileExtension' => 'File Extension',
            'FileData' => 'File Data',
            'TrackingNumber' => 'Tracking Number',
            'TrackingUrl' => 'Tracking Url',
            'ReferenceNumber' => 'Reference Number',
            'PageSize' => 'Page Size',
            'error_message' => 'Error Message',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
