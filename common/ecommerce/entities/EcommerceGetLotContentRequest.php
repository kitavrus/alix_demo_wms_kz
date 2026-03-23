<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_get_lot_content_request".
 *
 * @property int $id
 * @property int $our_inbound_id
 * @property string $BusinessUnitId
 * @property string $LotBarcode
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceGetLotContentRequest extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_get_lot_content_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['our_inbound_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['BusinessUnitId', 'LotBarcode'], 'string', 'max' => 64],
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
            'BusinessUnitId' => 'Business Unit ID',
            'LotBarcode' => 'Lot Barcode',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
