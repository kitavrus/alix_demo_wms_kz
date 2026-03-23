<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_cancel_shipment_response".
 *
 * @property int $id
 * @property int $cancel_shipment_request_id
 * @property int $our_outbound_id
 * @property string $IsSuccess
 * @property string $error_message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceCancelShipmentResponse extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_cancel_shipment_response';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cancel_shipment_request_id','our_outbound_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['error_message'], 'string'],
            [['IsSuccess'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cancel_shipment_request_id' => 'Cancel Shipment Request ID',
            'IsSuccess' => 'Is Success',
            'error_message' => 'Error Message',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
