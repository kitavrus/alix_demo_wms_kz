<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_send_shipment_feedback_request".
 *
 * @property int $id
 * @property string $our_outbound_id
 * @property string $BusinessUnitId
 * @property string $ShipmentId
 * @property string $SkuId
 * @property string $SkuBarcode
 * @property string $Quantity
 * @property string $WaybillSerial
 * @property string $WaybillNumber
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceSendShipmentFeedbackRequest extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_send_shipment_feedback_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['our_outbound_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['BusinessUnitId', 'ShipmentId', 'SkuId', 'SkuBarcode', 'Quantity', 'WaybillSerial', 'WaybillNumber'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'our_outbound_id' => Yii::t('app', 'our outbound id'),
            'BusinessUnitId' => Yii::t('app', 'Business Unit ID'),
            'ShipmentId' => Yii::t('app', 'Shipment ID'),
            'SkuId' => Yii::t('app', 'Sku ID'),
            'SkuBarcode' => Yii::t('app', 'Sku Barcode'),
            'Quantity' => Yii::t('app', 'Quantity'),
            'WaybillSerial' => Yii::t('app', 'Waybill Serial'),
            'WaybillNumber' => Yii::t('app', 'Waybill Number'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
