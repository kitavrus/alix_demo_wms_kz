<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_get_lot_content_response".
 *
 * @property int $id
 * @property int $our_inbound_id
 * @property int $get_lot_content_id
 * @property string $LotBarcode
 * @property string $ProductBarcode
 * @property string $Quantity
 * @property string $error_message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceGetLotContentResponse extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_get_lot_content_response';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['our_inbound_id', 'get_lot_content_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['error_message'], 'string'],
            [['LotBarcode', 'ProductBarcode', 'Quantity'], 'string', 'max' => 64],
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
            'get_lot_content_id' => 'Get Lot Content ID',
            'LotBarcode' => 'Lot Barcode',
            'ProductBarcode' => 'Product Barcode',
            'Quantity' => 'Quantity',
            'error_message' => 'Error Message',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
