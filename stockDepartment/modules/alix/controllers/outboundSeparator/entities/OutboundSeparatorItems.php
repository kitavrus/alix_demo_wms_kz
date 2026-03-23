<?php
namespace stockDepartment\modules\alix\controllers\outboundSeparator\entities;

use Yii;

/**
 * This is the model class for table "outbound_separator_items".
 *
 * @property int $id
 * @property int $outbound_separator_id OutboundSeparator id
 * @property int $outbound_id Outbound id
 * @property string $order_number Order number
 * @property string $out_box_barcode
 * @property string $product_barcode
 * @property string $status new,scanned
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class OutboundSeparatorItems extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_separator_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outbound_separator_id','outbound_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['order_number', 'out_box_barcode', 'product_barcode', 'status'], 'string', 'max' => 256],
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
            'outbound_id' => 'Outbound ID',
            'order_number' => 'Order Number',
            'out_box_barcode' => 'Outbound Box Barcode',
            'product_barcode' => 'Product Barcode',
            'status' => 'Status',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
