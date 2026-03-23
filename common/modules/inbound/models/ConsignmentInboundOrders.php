<?php

namespace common\modules\inbound\models;

use common\models\ActiveRecord;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use common\modules\inbound\models\InboundOrder;

/**
 * This is the model class for table "consignment_inbound_orders".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $from_point_id
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
class ConsignmentInboundOrders extends \common\models\ActiveRecord
{
//    const DELIVERY_TYPE_RPT = 1; // RPT
//    const DELIVERY_TYPE_CROSS_DOCK = 2; // CROSS-DOCK
      //const DELIVERY_TYPE_CROSS_DOCK_A = 3; // CROSS-DOCK COLINS

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'consignment_inbound_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_type','from_point_id','client_id',  'order_type', 'status', 'expected_qty', 'accepted_qty', 'allocated_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
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
            'from_point_id' => Yii::t('forms', 'From point'),
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
    * Get inbound party by client id and in status new and in process
    * @return integer $clientID Client id
    * @return array
    * */
    public static function getNewAndInProcessItemByClientID($clientID)
    {
        return ArrayHelper::map(
                                self::find()
                                ->select('id, party_number, from_point_id')
                                ->where(['status'=>[
                                    Stock::STATUS_INBOUND_NEW,
                                    Stock::STATUS_INBOUND_SCANNING,
                                    Stock::STATUS_INBOUND_SCANNED
                                ],'client_id'=>$clientID])
                                ->andWhere(['deleted'=>ActiveRecord::NOT_SHOW_DELETED])
                                ->asArray()->all(),'id', function($data, $defaultValue) {
                                    $store = '';
                                    if($s = Store::findOne($data['from_point_id'])) {
                                        $store = $s->getPointTitleByPattern('{city_name} {shopping_center_name} {name}');
                                    }

                                    return $data['party_number'].' -> '.$store;
                                });
    }

   /*
    * Get inbound orders by client id and in status new and in process
    * @return integer $partyID Self id
    * @return array
    * */
    public static function getNewAndInProcessOrdersByPartyID($partyID)
    {
        return ArrayHelper::map(
                InboundOrder::find()
                  ->select('id, order_number')
                  ->where(['status'=>[
                      Stock::STATUS_INBOUND_NEW,
                      Stock::STATUS_INBOUND_SCANNING,
                      Stock::STATUS_INBOUND_SCANNED
                  ],'consignment_inbound_order_id'=>$partyID])
                  ->andWhere(['deleted'=>ActiveRecord::NOT_SHOW_DELETED])
                  ->asArray()->all(),
              'id',
              'order_number');
    }

    /*
   * Get inbound order items in this party
   * @return array
   * */
    public function getPartyItems()
    {

        $inboundOrdersId = InboundOrder::find()->select('id')
            ->andWhere(['consignment_inbound_order_id'=>$this->id])
            ->andWhere(['in', 'status', [
                Stock::STATUS_INBOUND_NEW,
                Stock::STATUS_INBOUND_SORTING,
                Stock::STATUS_INBOUND_SORTED,
                Stock::STATUS_INBOUND_SCANNING,
            ]])
            ->asArray()
            ->column();
        //VarDumper::dump($inboundOrdersId, 10, true); die;
        $data = InboundOrderItem::find()->andWhere(['inbound_order_id' => $inboundOrdersId])
                                        ->andWhere('expected_qty != accepted_qty')
                                        ->limit(100)
                                        ->asArray()
                                        ->all();

        return $data;
    }

    /*
     * Get count order accepted
     * */
    public static function getCountAcceptedOrder()
    {

    }

    /*
     * Relation has one with InboundOrder
     * */
    public function getInboundOrders()
    {
        return $this->hasMany(InboundOrder::className(), ['consignment_inbound_order_id' => 'id']);
    }

    /*
      * Get count order accepted
      * */
    public function recalculateOrderItems()
    {
        if($orders = $this->inboundOrders){
            $expectedQty = 0;
            $acceptedQty = 0;
            foreach ($orders as $order){
                $expectedQty += $order->expected_qty;
                $acceptedQty += $order->accepted_qty;
            }

            $this->expected_qty = $expectedQty;
            $this->accepted_qty = $acceptedQty;
            $this->save(false);
        }
    }
}
