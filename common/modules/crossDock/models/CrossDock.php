<?php

namespace common\modules\crossDock\models;

use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use yii\helpers\ArrayHelper;
use common\modules\store\models\Store;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\crossDock\models\CrossDockItems;
use common\components\DeliveryProposalManager;
/**
 * This is the model class for table "cross_dock".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $consignment_cross_dock_id
 * @property integer $from_point_id
 * @property integer $to_point_id
 * @property string $to_point_title
 * @property string $from_point_title
 * @property string $internal_barcode
 * @property string $party_number
 * @property string $order_number
 * @property integer $order_type
 * @property integer $status
 * @property integer $cargo_status
 * @property integer $accepted_number_places_qty
 * @property integer $expected_number_places_qty
 * @property string $box_m3
 * @property string $weight_net
 * @property string $weight_brut
 * @property integer $expected_datetime
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $accepted_datetime
 * @property integer $date_left_warehouse
 * @property integer $date_delivered
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class CrossDock extends \common\models\ActiveRecord
{

    // Статус груза
    const CARGO_STATUS_NEW = 1; //новый
    const CARGO_STATUS_IN_PROCESSING = 2; //в обработке
    const CARGO_STATUS_ON_ROUTE = 3; //в пути
    const CARGO_STATUS_DELIVERED = 4; //доставлен

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cross_dock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accepted_datetime','consignment_cross_dock_id','client_id', 'from_point_id', 'to_point_id', 'order_type', 'status', 'cargo_status', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'date_delivered', 'begin_datetime', 'end_datetime', 'date_left_warehouse', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['to_point_title', 'from_point_title'], 'string', 'max' => 255],
            [['weight_brut','weight_net','box_m3','party_number', 'order_number', 'internal_barcode'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'client_id' => Yii::t('inbound/forms', 'Client ID'),
            'from_point_id' => Yii::t('stock/forms', 'From point ID'),
            'to_point_id' => Yii::t('stock/forms', 'To point ID'),
            'to_point_title' => Yii::t('inbound/forms', 'To Point Title'),
            'from_point_title' => Yii::t('inbound/forms', 'From Point Title'),
            'internal_barcode' => Yii::t('inbound/forms', 'Barcode'),
            'party_number' => Yii::t('inbound/forms', 'Party number'),
            'order_number' => Yii::t('inbound/forms', 'Order number'),
            'order_type' => Yii::t('inbound/forms', 'Order Type'),
            'status' => Yii::t('inbound/forms', 'Status'),
            'status_cargo' => Yii::t('inbound/forms', 'Cargo Status'),
            'accepted_number_places_qty' => Yii::t('inbound/forms', 'Accepted Number Places Qty'),
            'expected_number_places_qty' => Yii::t('inbound/forms', 'Expected Number Places Qty'),

            'box_m3' => Yii::t('stock/forms', 'Box volume'),
            'weight_brut' => Yii::t('stock/forms', 'Brut weight'),
            'weight_net' => Yii::t('stock/forms', 'Net weight'),
            'accepted_datetime' => Yii::t('outbound/forms', 'Accepted datetime'),
            'date_left_warehouse' => Yii::t('outbound/forms', 'Date left warehouse'),
            'date_delivered' => Yii::t('outbound/forms', 'Date delivered'),

            'expected_datetime' => Yii::t('inbound/forms', 'Expected Datetime'),
            'begin_datetime' => Yii::t('inbound/forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('inbound/forms', 'End Datetime'),
            'created_user_id' => Yii::t('inbound/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('inbound/forms', 'Updated User ID'),
            'created_at' => Yii::t('inbound/forms', 'Created At'),
            'updated_at' => Yii::t('inbound/forms', 'Updated At'),
            'deleted' => Yii::t('inbound/forms', 'Deleted'),
        ];
    }

    /**
     * @return array Массив с статусами груза.
     */
    public static function getCargoStatusArray()
    {
        return [
            self::CARGO_STATUS_NEW => Yii::t('stock/titles', 'New'),
            self::CARGO_STATUS_IN_PROCESSING => Yii::t('stock/titles', 'In processing at the warehouse'),
            self::CARGO_STATUS_ON_ROUTE => Yii::t('stock/titles', 'On route'),
            self::CARGO_STATUS_DELIVERED => Yii::t('stock/titles', 'Delivered'),
        ];
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getCargoStatusValue($cargo_status = null)
    {
        if(is_null($cargo_status)){
            $cargo_status = $this->cargo_status;
        }
        return ArrayHelper::getValue(self::getCargoStatusArray(), $cargo_status);
    }

    /*
    * Get Cross Dock orders for specified client
    * @param integer $clientID
    * @return array
    * */
    public static function getCrossDockListByClientID($clientID)
    {
        return ArrayHelper::map(self::find()
            ->select('id, party_number')
            ->andWhere([
                'client_id'=>$clientID
            ])
            ->andWhere(['in', 'status', [
                Stock::STATUS_CROSS_DOCK_NEW,
                Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST,
                Stock::STATUS_CROSS_DOCK_SCANNING,
                Stock::STATUS_CROSS_DOCK_SCANNED,
            ]])
            ->groupBy('party_number')
            ->asArray()
            ->all(),
            'party_number', 'party_number');
    }

    /*
    * Get Cross Dock orders for specified client
    * @param integer $clientID
    * @return array
    * */
    public static function getCrossDockCompleteListByClientID($clientID)
    {
        return ArrayHelper::map(self::find()
            ->select('id, party_number')
            ->andWhere([
                'client_id'=>$clientID
            ])
            ->andWhere(['in', 'cargo_status', [
                self::CARGO_STATUS_ON_ROUTE,
                self::CARGO_STATUS_DELIVERED,
            ]])->orWhere(['in', 'status', [
                Stock::STATUS_CROSS_DOCK_COMPLETE,
            ]])
            ->groupBy('party_number')
            ->orderBy(['id' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all(),
            'party_number', 'party_number');
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getPointFrom()
    {
        return $this->hasOne(Store::className(), ['id' => 'from_point_id']);
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getPointTo()
    {
        return $this->hasOne(Store::className(), ['id' => 'to_point_id']);
    }

    /* Generate CD barcode if null
     * format: client_id - party_number
     **/
    public function assignBarcode()
    {
        if(is_null($this->internal_barcode)){
            $this->internal_barcode = $this->client_id . '-' .$this->party_number;
        }
    }

    /*
   * Relation has many with InboundOrderItem
   * */
    public function getOrderItems()
    {
        return $this->hasMany(CrossDockItems::className(), ['cross_dock_id' => 'id']);
    }

    /*
     * Create Delivery Proposal based on CD orders
     * If DP already exist, then add Delivery Order
     * to this one
     * @return bool
     *
     * */
    public function createDeliveryProposal()
    {
        if($dp = TlDeliveryProposal::find()
            ->andWhere([
                'route_from'=>$this->from_point_id,
                'route_to' =>$this->to_point_id,
                'client_id' => $this->client_id,
                'status'=>TlDeliveryProposal::STATUS_NEW
            ])->one())
        {
            $shopCode='';
            if($point = $dp->routeTo){
                $shopCode = $point->shop_code;
            }
            $deliveryOrder = new  TlDeliveryProposalOrders();
            $deliveryOrder->client_id = $dp->client_id;
            $deliveryOrder->tl_delivery_proposal_id = $dp->id;
            $deliveryOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
            $deliveryOrder->order_number = $this->order_number;
            $deliveryOrder->order_id = $this->id;
            $deliveryOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK;
            $deliveryOrder->number_places = $this->accepted_number_places_qty;
            $deliveryOrder->number_places_actual = $this->accepted_number_places_qty;
            $deliveryOrder->mc_actual = floatval($this->box_m3);
            $deliveryOrder->mc = floatval($this->box_m3);
            $deliveryOrder->kg_actual = floatval($this->weight_brut);
            $deliveryOrder->kg = floatval($this->weight_brut);
            $deliveryOrder->order_number ='cross-dock['.$this->party_number.' / '.ltrim($this->internal_barcode,'2-').']';
            $deliveryOrder->save(false);

            $dpManager = new DeliveryProposalManager(['id' => $dp->id]);
            $dpManager->onCreateProposal();

            return true;

        } else {
            $dp = new TlDeliveryProposal();
            $dp->client_id = $this->client_id;
            $dp->source = TlDeliveryProposal::SOURCE_AUTO_CROSS_DOCK;
            $dp->route_from = $this->from_point_id;
            $dp->route_to = $this->to_point_id;
//            $dp->number_places = $this->expected_number_places_qty;
//            $dp->number_places_actual = $this->accepted_number_places_qty;
//            $dp->mc_actual = floatval($this->box_m3);
//            $dp->mc = floatval($this->box_m3);
            $dp->status = TlDeliveryProposal::STATUS_NEW;
            $dp->cash_no = TlDeliveryProposal::METHOD_CHAR;
            if($dp->save(false)) {
                $this->createDeliveryProposal();
            }

        }

        return false;
    }

    /*
     * Add Accepted DateTime
     * @return void
     * */
    public function addAcceptedDateTime() {
        $this->accepted_datetime = time();
    }
	
	


    /*
 * */
    public function _intervalH($interval,$workingDays = 0)
    {
        $plus = 0;
        if($interval->i >=30) {
            $plus = 1; // добавляем один час если если больше 30 мин.
        }
        if($workingDays == null) {
            $workingDays = $interval->d;
        }

        if($workingDays == 0) {
            if($interval->h <= 0) {
                return 1; // Один час
            }
            return $interval->h+$plus;
        }

        return ($workingDays * 24) + $interval->h + $plus;

    }

    /*
     * Calculate WMS
     * data_created_on_client minus packing_date
     * @return mixed
     **/
    public function calculateWMS()
    {
        if(empty($this->created_at) || empty($this->accepted_datetime)){
            return '-';
        }

        $start = new \DateTime();
        $start->setTimestamp($this->created_at);
        $end = new \DateTime();
        $end->setTimestamp($this->accepted_datetime);

        $interval = $start->diff($end);
        $workingDays = 0;
        for ($i=0; $i<$interval->d; $i++){
            $start->modify('+1 day');
            $weekday = $start->format('w');

            if($weekday != 0 && $weekday != 6){ // 0 for Sunday and 6 for Saturday
                $workingDays++;
            }
        }

        return $this->_intervalH($interval,$workingDays);
    }

    /*
     * Calculate TR
     * date_left_warehouse minus date_delivered
     * @return mixed
     **/
    public function calculateTR()
    {
        if(empty($this->date_delivered) || empty($this->date_left_warehouse)){
            return '-';
        }

        $start = new \DateTime();
        $start->setTimestamp($this->date_left_warehouse);
        $end = new \DateTime();
        $end->setTimestamp($this->date_delivered);

        $interval = $start->diff($end);
        $workingDays = 0;
        for ($i = 0; $i < $interval->d; $i++) {
            $start->modify('+1 day');
            $weekday = $start->format('w');

            if($weekday != 0 && $weekday != 6) { // 0 for Sunday and 6 for Saturday
                $workingDays++;
            }
        }
        return $this->_intervalH($interval,$workingDays);
    }

    /*
     * Calculate TR
     * date_left_warehouse minus date_delivered
     * @return mixed
     **/
    public function calculateFULL()
    {
        if(empty($this->created_at) || empty($this->date_delivered)){
            return '-';
        }

        $start = new \DateTime();
        $start->setTimestamp($this->created_at);
//        $start->setTimestamp($this->data_created_on_client);
        $end = new \DateTime();
        $end->setTimestamp($this->date_delivered);
        $interval = $start->diff($end);
        $workingDays = 0;
        for ($i=0; $i<$interval->d; $i++) {
            $start->modify('+1 day');
            $weekday = $start->format('w');

            if($weekday != 0 && $weekday != 6){ // 0 for Sunday and 6 for Saturday
                $workingDays++;
            }
        }

        return $this->_intervalH($interval,$workingDays);
    }
}
