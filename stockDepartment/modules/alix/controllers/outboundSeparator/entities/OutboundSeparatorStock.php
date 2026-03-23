<?php
namespace stockDepartment\modules\alix\controllers\outboundSeparator\entities;

use Yii;

/**
 * This is the model class for table "outbound_separator_stock".
 *
 * @property int $id
 * @property int $outbound_separator_id OutboundSeparator id
 * @property int $stock_id stock id
 * @property int $outbound_id Outbound id
 * @property string $order_number Order number
 * @property string $out_box_barcode Box 4000
 * @property string $in_box_barcode Box 5000
 * @property int $product_id Product id
 * @property string $product_sku Product sku
 * @property string $product_barcode Product barcode
 * @property string $status new,scanned
 * @property string $status_to_out Не отгружать
 * @property string $stock_data JSON stock data
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class OutboundSeparatorStock extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_separator_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["outbound_separator_id ",'stock_id', 'outbound_id', 'product_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['stock_data'], 'string'],
            [['order_number', 'in_box_barcode','out_box_barcode', 'product_sku', 'product_barcode', 'status', 'status_to_out'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
			'outbound_separator_id' => 'Outbound separator ID',
            'stock_id' => 'Stock ID',
            'outbound_id' => 'Outbound ID',
            'order_number' => 'Order Number',
            'out_box_barcode' => 'Outbound Box Barcode',
            'in_box_barcode' => 'Inbound Box Barcode',
            'product_id' => 'Product ID',
            'product_sku' => 'Product Sku',
            'product_barcode' => 'Product Barcode',
            'status' => 'Status',
            'status_to_out' => 'Status To Out',
            'stock_data' => 'Stock Data',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
