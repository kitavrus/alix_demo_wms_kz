<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_stock_adjustment_request".
 *
 * @property int $id
 * @property string $BusinessUnitId
 * @property string $LotOrSingleBarcode
 * @property string $Quantity
 * @property string $Operator
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceStockAdjustmentRequest extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_stock_adjustment_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['BusinessUnitId', 'LotOrSingleBarcode', 'Quantity', 'Operator'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'BusinessUnitId' => 'Business Unit ID',
            'LotOrSingleBarcode' => 'Lot Or Single Barcode',
            'Quantity' => 'Quantity',
            'Operator' => 'Operator',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
