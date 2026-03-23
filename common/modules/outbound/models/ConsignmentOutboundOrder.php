<?php

namespace common\modules\outbound\models;

use common\modules\stock\models\Stock;
use Yii;

/**
 * This is the model class for table "consignment_outbound_orders".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $party_number
 * @property integer $order_type
 * @property integer $delivery_type
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $allocated_qty
 * @property integer $accepted_number_places_qty
 * @property integer $expected_number_places_qty
 * @property integer $expected_datetime
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ConsignmentOutboundOrder extends \common\models\ActiveRecord
{

//    const DELIVERY_TYPE_RPT = 1; // RPT
//    const DELIVERY_TYPE_CROSS_DOCK = 2; // CROSS-DOCK

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'consignment_outbound_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_type','allocated_qty','client_id', 'order_type', 'status', 'expected_qty', 'accepted_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['party_number',], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'client_id' => Yii::t('forms', 'Client ID'),
            'party_number' => Yii::t('forms', 'Party Number'),
            'order_type' => Yii::t('forms', 'Order Type'),
            'delivery_type' => Yii::t('forms', 'Delivery type'),
            'status' => Yii::t('forms', 'Status'),
            'expected_qty' => Yii::t('forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('forms', 'Accepted Qty'),
            'allocated_qty' => Yii::t('forms', 'Allocated Qty'),
            'accepted_number_places_qty' => Yii::t('forms', 'Accepted Number Places Qty'),
            'expected_number_places_qty' => Yii::t('forms', 'Expected Number Places Qty'),
            'expected_datetime' => Yii::t('forms', 'Expected Datetime'),
            'begin_datetime' => Yii::t('forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('forms', 'End Datetime'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }

    /*
     * Set complete
     * @param integer $consignmentOutboundOrderId
     * @return mix
     * */
    public static function checkAndSetStatusComplete($consignmentOutboundOrderId)
    {
        if(!empty($consignmentOutboundOrderId)) {
            $all = OutboundOrder::find()->where(['consignment_outbound_order_id' => $consignmentOutboundOrderId])->count();
            $complete = OutboundOrder::find()->where([
                'consignment_outbound_order_id' => $consignmentOutboundOrderId,
                'status' => [
                    Stock::STATUS_OUTBOUND_COMPLETE,
                    Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_ON_ROAD,
                    Stock::STATUS_OUTBOUND_DELIVERED,
                    Stock::STATUS_OUTBOUND_DONE,
                ]
            ])->count();

            if (($all == $complete) && ($consignmentModel = ConsignmentOutboundOrder::findOne($consignmentOutboundOrderId))) {
                $consignmentModel->status = Stock::STATUS_OUTBOUND_COMPLETE;
                $consignmentModel->save(false);
            }
        }

        return null;
    }

    /*
   * Relation has many with OutboundOrders
   * */
    public function getOrders()
    {
        return $this->hasMany(OutboundOrder::className(), ['consignment_outbound_order_id' => 'id']);
    }

    /*
   * Get count order accepted
   * */
    public function recalculateOrderItems()
    {
        if($orders = $this->orders){
            $expectedQty = 0;
            $acceptedQty = 0;
            $allocatedQty = 0;
            foreach ($orders as $order){
                $expectedQty += $order->expected_qty;
                $acceptedQty += $order->accepted_qty;
                $allocatedQty += $order->allocated_qty;
            }

            $this->expected_qty = $expectedQty;
            $this->accepted_qty = $acceptedQty;
            $this->allocated_qty = $allocatedQty;
            $this->save(false);
        }
    }
}
