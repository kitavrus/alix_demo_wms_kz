<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_get_inbound_data_response".
 *
 * @property int $id
 * @property int $our_inbound_id
 * @property int $get_inbound_data_id
 * @property string $InboundId
 * @property string $FromBusinessUnitId
 * @property string $LcOrCartonLabel
 * @property string $NumberOfCartons
 * @property string $SkuId
 * @property string $LotOrSingleBarcode
 * @property string $LotOrSingleQuantity
 * @property string $Status
 * @property string $ToBusinessUnitId
 * @property string $error_message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceGetInboundDataResponse extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_get_inbound_data_response';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['our_inbound_id', 'get_inbound_data_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['error_message'], 'string'],
            [['InboundId', 'FromBusinessUnitId', 'LcOrCartonLabel', 'NumberOfCartons', 'SkuId', 'LotOrSingleBarcode', 'LotOrSingleQuantity', 'Status', 'ToBusinessUnitId'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'our_inbound_id' => 'Our Inbound ID',
            'get_in_bound_data_id' => 'Get In Bound Data ID',
            'InboundId' => 'Inbound ID',
            'FromBusinessUnitId' => 'From Business Unit ID',
            'LcOrCartonLabel' => 'Lc Or Carton Label',
            'NumberOfCartons' => 'Number Of Cartons',
            'SkuId' => 'Sku ID',
            'LotOrSingleBarcode' => 'Lot Or Single Barcode',
            'LotOrSingleQuantity' => 'Lot Or Single Quantity',
            'Status' => 'Status',
            'ToBusinessUnitId' => 'To Business Unit ID',
            'error_message' => 'Error Message',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
