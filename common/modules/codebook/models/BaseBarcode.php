<?php

namespace common\modules\codebook\models;

use Yii;

/**
 * This is the model class for table "base_barcodes".
 *
 * @property integer $id
 * @property integer $tl_delivery_proposal_id
 * @property integer $outbound_id
 * @property integer $inbound_id
 * @property integer $order_type
 * @property string $base_barcode
 * @property string $box_barcode
 * @property integer $box_number
 * @property integer $box_total
 * @property integer $ttn_barcode
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class BaseBarcode extends \common\models\ActiveRecord
{
    const ORDER_TYPE_OUTBOUND = 1;
    const ORDER_TYPE_CROSS_DOCK = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'base_barcodes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inbound_id','outbound_id','tl_delivery_proposal_id','box_total','order_type', 'box_number', 'ttn_barcode', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['base_barcode', 'box_barcode'], 'string', 'max' => 34]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'inbound_id' => Yii::t('forms', 'Inbound ID'),
            'outbound_id' => Yii::t('forms', 'Outbound ID'),
            'tl_delivery_proposal_id' => Yii::t('forms', 'Delivery proposal id'),
            'order_type' => Yii::t('forms', 'Order Type'),
            'base_barcode' => Yii::t('forms', 'Base Barcode'),
            'box_barcode' => Yii::t('forms', 'Box Barcode'),
            'box_number' => Yii::t('forms', 'Box Number'),
            'box_total' => Yii::t('forms', 'Box total'),
            'ttn_barcode' => Yii::t('forms', 'Ttn Barcode'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }
}
