<?php

namespace common\modules\outbound\models;

use common\helpers\iHelper;
use common\modules\client\models\Client;
use common\modules\stock\models\ConstantZone;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use Yii;
use common\models\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use common\helpers\DateHelper;
use yii\helpers\Json;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\transportLogistics\components\TLHelper;

/**
 * This is the model class for table "outbound_orders".
 *
 * @property integer $id
 * @property string $client_order_id
 * @property integer $client_id
 * @property integer $supplier_id
 * @property integer $warehouse_id
 * @property integer $from_point_id
 * @property integer $to_point_id
 * @property integer $from_point_title
 * @property integer $to_point_title
 * @property integer $order_number
 * @property integer $zone
 * @property integer $delivery_type
 * @property integer $parent_order_number
 * @property integer $consignment_outbound_order_id
 * @property integer $order_type
 * @property integer $status
 * @property string  $extra_status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $date_confirm
 * @property integer $allocated_qty
 * @property integer $accepted_number_places_qty
 * @property integer $expected_number_places_qty
 * @property integer $allocated_number_places_qty
 * @property integer $expected_datetime
 * @property string packing_date
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property string  $data_created_on_client //Дата создания заказа в системе клиента
 * @property string  $extra_fields //Дата создания заказа в системе клиента
 * @property string  $date_left_warehouse // Дата создания заказа в системе клиента
 * @property string  $date_delivered // Дата доставки заказа в точку получения
 * @property string  $title // Название
 * @property string  $description // Описание
 * @property string  $api_send_data // Описание
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at // Дата создание заказа в нашей системе
 * @property integer $updated_at
 */
class OutboundOrder extends ActiveRecord
{
    /*
    * @var integer delivery type
    *
    * */
    const DELIVERY_TYPE_RPT = 1; // RPT
    const DELIVERY_TYPE_CROSS_DOCK = 2; // CROSS-DOCK
    const DELIVERY_TYPE_CROSS_DOCK_A = 3; // CROSS-DOCK COLINS

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
        return 'outbound_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zone','delivery_type','consignment_outbound_order_id','allocated_qty','allocated_number_places_qty','from_point_id','to_point_id','client_id', 'supplier_id', 'warehouse_id',  'order_type', 'status', 'cargo_status', 'expected_qty', 'accepted_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'date_confirm'], 'integer'],
            [['client_order_id','extra_status','extra_fields','date_delivered', 'packing_date', 'date_left_warehouse','data_created_on_client','from_point_title','to_point_title','parent_order_number','order_number', 'title', 'description'], 'string'],
            [['mc','kg'], 'number'],
            [['client_id', 'from_point_id', 'to_point_id'], 'required', 'on' => 'manual-create'],
			[['api_send_data'], 'string'],
			[['api_complete_status'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['manual-create'] = ['client_id', 'from_point_id', 'to_point_id', 'kg', 'mc', 'accepted_number_places_qty', 'title', 'description'];
        $scenarios['manual-update'] = [ 'kg', 'mc', 'accepted_number_places_qty','title', 'description'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'delivery_type' => Yii::t('outbound/forms', 'Delivery type'),
            'id' => Yii::t('outbound/forms', 'ID'),
            'client_id' => Yii::t('outbound/forms', 'Client ID'),
            'supplier_id' => Yii::t('outbound/forms', 'Supplier ID'),
            'warehouse_id' => Yii::t('outbound/forms', 'Warehouse ID'),
            'from_point_id' => Yii::t('outbound/forms', 'From point id'),
            'to_point_id' => Yii::t('outbound/forms', 'To point id'),
            'from_point_title' => Yii::t('outbound/forms', 'From point title'),
            'to_point_title' => Yii::t('outbound/forms', 'To point title'),
			'to_point_title' => Yii::t('outbound/forms', 'To point'),
            'order_number' => Yii::t('outbound/forms', 'Order number'),
            'zone' => Yii::t('outbound/forms', 'Zone'),
            'parent_order_number' => Yii::t('outbound/forms', 'Parent order number'),
            'consignment_outbound_order_id' => Yii::t('outbound/forms', 'Consignment id'),
            'order_type' => Yii::t('outbound/forms', 'Order Type'),
            'status' => Yii::t('outbound/forms', 'Status'),
            'cargo_status' => Yii::t('inbound/forms', 'Cargo Status'),
            'extra_status' => Yii::t('outbound/forms', 'Extra status'),
            'expected_qty' => Yii::t('outbound/forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('outbound/forms', 'Accepted Qty'),
            'allocated_qty' => Yii::t('outbound/forms', 'Allocate Qty'),
            'accepted_number_places_qty' => Yii::t('outbound/forms', 'Accepted Number Places Qty'),
            'expected_number_places_qty' => Yii::t('outbound/forms', 'Expected Number Places Qty'),
            'allocated_number_places_qty' => Yii::t('outbound/forms', 'Allocate Number Places Qty'),
            'expected_datetime' => Yii::t('outbound/forms', 'Expected Datetime'),
            'begin_datetime' => Yii::t('outbound/forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('outbound/forms', 'End Datetime'),
            'date_confirm' => Yii::t('outbound/forms', 'Confirmed At'),
            'data_created_on_client' => Yii::t('outbound/forms', 'Data created on client'),
            'packing_date' => Yii::t('outbound/forms', 'Packing date'),
            'date_left_warehouse' => Yii::t('outbound/forms', 'Date left our warehouse'),
            'date_delivered' => Yii::t('outbound/forms', 'Date delivered'),
            'created_user_id' => Yii::t('outbound/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('outbound/forms', 'Updated User ID'),
            'created_at' => Yii::t('outbound/forms', 'Created At'),
            'updated_at' => Yii::t('outbound/forms', 'Updated At'),
            'deleted' => Yii::t('outbound/forms', 'Deleted'),
            'mc' => Yii::t('outbound/forms', 'Volume (м³)'),
            'kg' => Yii::t('outbound/forms', 'Weight (kg)'),
            'title' => Yii::t('outbound/forms', 'Title'),
            'description' => Yii::t('outbound/forms', 'Description'),
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
    * Relation has many with OutboundOrderItem
    * */
    public function getOrderItems()
    {
        return $this->hasMany(OutboundOrderItem::className(), ['outbound_order_id' => 'id']);
    }

    /*
    * Relation has many with Stock
    * */
    public function getOrderItemInStock()
    {
        return $this->hasMany(Stock::className(), ['outbound_order_id' => 'id']);
    }

    /*
     * Get parent order number by client
     * @param integer $clientID
     * @return array parent_order_number
     * */
    public static function getParentOrderNumberByClientId($clientID)
    {
        return ArrayHelper::map(self::find()
									->select('parent_order_number')
									->andWhere(['client_id'=>$clientID])
									->andWhere(['status'=>[
										Stock::STATUS_OUTBOUND_FULL_RESERVED,
										Stock::STATUS_OUTBOUND_PART_RESERVED,
										Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
									]])
									->groupBy('parent_order_number')
									->asArray()
									->all(),'parent_order_number','parent_order_number');
    }

    /*
     * Get active parent order number by client
     * @param integer $clientID
     * @return array parent_order_number
     * */
    public static function getActiveParentOrderNumberByClientId($clientID)
    {
        $data = ConsignmentOutboundOrder::find()
                    ->select('party_number')
                    ->andWhere(['client_id'=>$clientID])
                    ->andWhere(['NOT IN','status',[Stock::STATUS_OUTBOUND_COMPLETE]])
                    ->asArray()->all();

        return ArrayHelper::map($data,'party_number','party_number');
    }

    /**
     * @return array Массив с статусами.
     */
    public function getStatusArray()
    {
        return [
            Stock::STATUS_OUTBOUND_CANCEL => Yii::t('stock/titles', 'Cancel'),
            Stock::STATUS_OUTBOUND_NEW => Yii::t('stock/titles', 'New'),
            Stock::STATUS_OUTBOUND_RESERVING => Yii::t('stock/titles', 'Process reserving'),
            Stock::STATUS_OUTBOUND_FULL_RESERVED => Yii::t('stock/titles', 'Full reserved'),//разные
            Stock::STATUS_OUTBOUND_PART_RESERVED => Yii::t('stock/titles', 'Part reserved'),//разные
            Stock::STATUS_OUTBOUND_PICKING => Yii::t('stock/titles', 'Picking'),//разные
            Stock::STATUS_OUTBOUND_PICKED => Yii::t('stock/titles', 'Picked'),//hfpyst
            Stock::STATUS_OUTBOUND_SORTING => Yii::t('stock/titles', 'Sorting'),
            Stock::STATUS_OUTBOUND_SORTED => Yii::t('stock/titles', 'Sorted'),
            Stock::STATUS_OUTBOUND_PACKING => Yii::t('stock/titles', 'Packing'),
            Stock::STATUS_OUTBOUND_PACKED => Yii::t('stock/titles', 'Packed'),
            Stock::STATUS_OUTBOUND_SHIPPING => Yii::t('stock/titles', 'Shipping'),
            Stock::STATUS_OUTBOUND_SHIPPED => Yii::t('stock/titles', 'Shipped'),
            Stock::STATUS_OUTBOUND_ON_ROAD => Yii::t('stock/titles', 'On road'),
            Stock::STATUS_OUTBOUND_DELIVERED => Yii::t('stock/titles', 'Delivered'),
            Stock::STATUS_OUTBOUND_SCANNING => Yii::t('stock/titles', 'Scanning'),//один
            Stock::STATUS_OUTBOUND_SCANNED => Yii::t('stock/titles', 'Scanned'),//один
            Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST => Yii::t('stock/titles', 'Printed picking list'),//разные
            Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API => Yii::t('stock/titles', 'File for API downloaded'),
            Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL => Yii::t('stock/titles', 'Print box label'), //выделить ярким цветом
            Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL => Yii::t('stock/titles', 'Printing box label'), //выделить ярким цветом
            Stock::STATUS_OUTBOUND_COMPLETE => Yii::t('stock/titles', 'Complete'),
            Stock::STATUS_OUTBOUND_DONE => Yii::t('stock/titles', 'Выполнен'),
        ];
    }


    /*
     * Get grid row color, depend on
     * record status
     * @return string
     **/
    public function getGridColor(){

        switch($this->status) {
            case Stock::STATUS_OUTBOUND_FULL_RESERVED: //#FFA54F
                $class = 'color-tan';
                break;
            case Stock::STATUS_OUTBOUND_PART_RESERVED: //#FFA500
                $class = 'color-orange';
                break;
            case Stock::STATUS_OUTBOUND_PICKING: //#FFF68F
                $class = 'color-khaki';
                break;
            case Stock::STATUS_OUTBOUND_PICKED: //#CAFF70
                $class = 'color-dark-olive-green';
                break;
            case Stock::STATUS_OUTBOUND_SCANNING: //#87CEFA
                $class = 'color-light-sky-blue';
                break;
            case Stock::STATUS_OUTBOUND_SCANNED: //#1E90FF
                $class = 'color-dodger-blue';
                break;
            case Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST: //#FFFFE0
                $class = 'color-light-yellow';
                break;
            case Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API: //#EE82EE
                $class = 'color-violet ';
                break;
            case Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL: //#FF6A6A
            case Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL: //#FF6A6A
                $class = 'color-indian-red';
                break;
            case Stock::STATUS_OUTBOUND_COMPLETE: //#C6E2FF
                $class = 'color-slate-gray';
                break;
            default:
                $class = '';
                break;

        }
        return $class;
    }

    /*
    * Get grid row color, depend on
    * record status
    * @return string
    **/
    public static function getGridColorByValue($status){

        switch($status) {
            case Stock::STATUS_OUTBOUND_FULL_RESERVED: //#FFA54F
                $class = 'color-tan';
                break;
            case Stock::STATUS_OUTBOUND_PART_RESERVED: //#FFA500
                $class = 'color-orange';
                break;
            case Stock::STATUS_OUTBOUND_PICKING: //#FFF68F
                $class = 'color-khaki';
                break;
            case Stock::STATUS_OUTBOUND_PICKED: //#CAFF70
                $class = 'color-dark-olive-green';
                break;
            case Stock::STATUS_OUTBOUND_SCANNING: //#87CEFA
                $class = 'color-light-sky-blue';
                break;
            case Stock::STATUS_OUTBOUND_SCANNED: //#1E90FF
                $class = 'color-dodger-blue';
                break;
            case Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST: //#FFFFE0
                $class = 'color-light-yellow';
                break;
            case Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API: //#EE82EE
                $class = 'color-violet ';
                break;
            case Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL: //#FF6A6A
            case Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL: //#FF6A6A
                $class = 'color-indian-red';
                break;
            case Stock::STATUS_OUTBOUND_COMPLETE: //#C6E2FF
                $class = 'color-slate-gray';
                break;
            default:
                $class = '';
                break;

        }
        return $class;
    }

    /*
     * Get grid row color, depend on
     * record status
     * @return string
     **/
    public function getClientGridColor(){

        switch($this->cargo_status) {
            case OutboundOrder::CARGO_STATUS_NEW : //#FFFFE0
                $class = 'color-light-yellow';
                break;
            case OutboundOrder::CARGO_STATUS_IN_PROCESSING : //#FFA54F
                $class = 'color-orange';
                break;
            case OutboundOrder::CARGO_STATUS_ON_ROUTE : //#FFA500
                $class = 'color-indian-red';
                break;
            case OutboundOrder::CARGO_STATUS_DELIVERED : //#FFA500
                $class = 'color-light-sky-blue';
                break;
            default:
                $class = '';
                break;

        }

        return $class;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatusValue($status = null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(), $status);
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getToPoint()
    {
        return $this->hasOne(Store::className(), ['id' => 'to_point_id']);
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getFromPoint()
    {
        return $this->hasOne(Store::className(), ['id' => 'from_point_id']);
    }


    /*
    * Relation has one with Client
    * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
    * Relation has one with ConsignmentOutboundOrder
    * */
    public function getParentOrder()
    {
        return $this->hasOne(ConsignmentOutboundOrder::className(), ['id' => 'consignment_outbound_order_id']);
    }

    /*
   * Array with attribute values functions mapping
   * @return array
   **/
    public function getAttributesValuesMap($attribute)
    {
        $data = [
            'status'=>'getStatusValue',
        ];

        return ArrayHelper::getValue($data, $attribute);
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
//        if(empty($this->data_created_on_client) || empty($this->packing_date)){
        if(empty($this->created_at) || empty($this->packing_date)){
            return '-';
        }

        $start = new \DateTime();
        $start->setTimestamp($this->created_at);
        $end = new \DateTime();
        $end->setTimestamp($this->packing_date);

        $interval = $start->diff($end);
        $workingDays = 0;
        for ($i=0; $i<$interval->d; $i++){
            $start->modify('+1 day');
            $weekday = $start->format('w');

            if($weekday != 0 && $weekday != 6){ // 0 for Sunday and 6 for Saturday
                $workingDays++;
            }
        }

//        $t = (($this->created_at - $this->packing_date) / 60) / 60;
//        VarDumper::dump($interval,10,true);
        return $this->_intervalH($interval,$workingDays);

        //if($workingDays == 0){
           // if($interval->h <= 0) {
           //     return iHelper::formatTextAfterNumber(1, "час", "часа", "часов");
//                return $t.'<br />'.iHelper::formatTextAfterNumber(1, Yii::t('titles', 'hour'), Yii::t('titles', 'hour'), Yii::t('titles', 'hour'));
            //}
           // return iHelper::formatTextAfterNumber($interval->h, Yii::t('titles', 'hour'), Yii::t('titles', 'chasa'), Yii::t('titles', 'hours'));
        //}
//        return $workingDays.'<br />'
//               .$t.'<br />'
//               .$interval->h.'<br />';
        //return iHelper::formatTextAfterNumber($workingDays, Yii::t('titles', 'day'), Yii::t('titles', 'dnya'), Yii::t('titles', 'days'));
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
//        if($workingDays == 0) {
//            if($interval->h <= 0) {
//                return iHelper::formatTextAfterNumber(1, Yii::t('titles', 'hour'), Yii::t('titles', 'hour'), Yii::t('titles', 'hour'));
//            }
//            return iHelper::formatTextAfterNumber($interval->h, Yii::t('titles', 'hour'), Yii::t('titles', 'chasa'), Yii::t('titles', 'hours'));
//        }
//        return iHelper::formatTextAfterNumber($workingDays, Yii::t('titles', 'day'), Yii::t('titles', 'dnya'), Yii::t('titles', 'days'));
    //}
       //     return iHelper::formatTextAfterNumber($interval->h, Yii::t('titles', 'hour'), Yii::t('titles', 'chasa'), Yii::t('titles', 'hours'));
      //  }

      //  return iHelper::formatTextAfterNumber($workingDays, Yii::t('titles', 'day'), Yii::t('titles', 'dnya'), Yii::t('titles', 'days'));
    }

    /*
     * Calculate TR
     * date_left_warehouse minus date_delivered
     * @return mixed
     **/
    public function calculateFULL()
    {
        if(empty($this->created_at) || empty($this->date_delivered)){
//        if(empty($this->data_created_on_client) || empty($this->date_delivered)){
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

//        if($workingDays == 0){
//            if($interval->h <= 0){
//                return iHelper::formatTextAfterNumber(1, Yii::t('titles', 'hour'), Yii::t('titles', 'hour'), Yii::t('titles', 'hour'));
//            }
//            return iHelper::formatTextAfterNumber($interval->h, Yii::t('titles', 'hour'), Yii::t('titles', 'chasa'), Yii::t('titles', 'hours'));
//        }
//
//        return iHelper::formatTextAfterNumber($workingDays, Yii::t('titles', 'day'), Yii::t('titles', 'dnya'), Yii::t('titles', 'days'));
    }

    /*
     * This method is called at the beginning of inserting or updating a record.
     * @param bool $insert
     * @return bool
     **/
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
//            $old = $this->getOldAttribute('status');
//            $new = $this->getAttribute('status');
//            if($old != $new && $new == Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL){
//                $this->packing_date = DateHelper::getTimestamp();
//            }

//            if($insert){
//                $this->cargo_status = self::CARGO_STATUS_NEW;
//            }
            //выставлям статус груза в зависимости от главного статуса
            $this->updateCargoStatus();
            return true;
        } else {
            return false;
        }
    }

    /*
    * Get count product in box by outbound order
    * @param string $boxBarcode
    * @param integer $id
    * @return integer Count in bpx
    * */
    public static function getCountInBoxById($boxBarcode,$id)
    {
        return Stock::find()->where([
            'box_barcode'=>$boxBarcode,
            'status'=>[Stock::STATUS_OUTBOUND_SCANNED,Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL],
            'outbound_order_id'=>$id,
        ])->count();
    }

    /*
    * Get expected_qty, accepted_qty, allocated_qty by Outbound order Id
    * @param integer $id
    * @return array
    * */
    public static function getAccExpById($id)
    {

        return OutboundOrder::find()->select('expected_qty, accepted_qty, allocated_qty')->where([
            'id'=>$id,
        ])->asArray()->one();
    }

    /*
    * Get items by Outbound order Id
    * @param integer $id
    * @param bool show only qty different records
    * @return array
    * */
    public static function getItemsById($id, $showDifferent=false)
    {
        $query =  OutboundOrderItem::find()->where([
            'outbound_order_id'=>$id,
        ]);

        if($showDifferent){
            $query->andWhere('expected_qty != accepted_qty');
        }

        return $query->asArray()->all();
    }

    /*
    * Save extra filed value
    * @param string $name filed name
    * @param string $value filed value
    * @param bool $add filed value
    * @return boolean
    * */
    public function saveExtraFieldValue($name, $value, $add = false)
    {
        $extraField = (array)Json::decode($this->extra_fields);
        if($add){
            if(isset($extraField[$name])){
                $extraField[$name] .= ';'. $value;
            } else {
                $extraField[$name] = $value;
            }

        } else {
            $extraField[$name] = $value;
        }

        $this->extra_fields = Json::encode($extraField);
        $this->save(false);

        return true;
    }

    /*
   * Get count order accepted
   * */
    public function recalculateOrderItems()
    {
        if($items = $this->orderItems){
            $expectedQty = 0;
            $acceptedQty = 0;
            $allocatedQty = 0;
            foreach ($items as $item){
                $expectedQty += $item->expected_qty;
                $acceptedQty += $item->accepted_qty;
                $allocatedQty += $item->allocated_qty;
            }

            $this->expected_qty = $expectedQty;
            $this->accepted_qty = $acceptedQty;
            $this->allocated_qty = $allocatedQty;
            $this->save(false);
        }
    }

    /*
     *Считаем кол-во предпологаемых и зарезерв. товаров
     * Если равны выставляем статус полностью зарезерв.
     **/
    public function checkOrderReservedStatus()
    {
        if($this->expected_qty == $this->allocated_qty){
            $this->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
            $this->save(false);
        }
    }

    /*
    * Create Delivery Proposal based on Outbound order
    * If DP already exist, then add Delivery Order
    * to this one
    * @return bool
    *
    * */
    public function createDeliveryProposal($orderNumber = null)
    {
        if (is_null($orderNumber)){
            $orderNumber = 'cross-dock-colins['.$this->order_number.']';
        }

        if($dp = TlDeliveryProposal::find()
            ->andWhere([
                'route_to' =>$this->to_point_id,
                'route_from' =>$this->from_point_id,
                'client_id' => $this->client_id,
                'status'=>TlDeliveryProposal::STATUS_NEW
            ])->one())
        {
            $deliveryOrder = new  TlDeliveryProposalOrders();
            $deliveryOrder->client_id = $dp->client_id;
            $deliveryOrder->tl_delivery_proposal_id = $dp->id;
            $deliveryOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
            $deliveryOrder->order_id = $this->id;
            $deliveryOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
//            $deliveryOrder->number_places = $this->expected_number_places_qty;
//            $deliveryOrder->number_places_actual = $this->accepted_number_places_qty;
//            $deliveryOrder->mc_actual = floatval($this->box_m3);
//            $deliveryOrder->mc = floatval($this->box_m3);
//            $deliveryOrder->kg_actual = floatval($this->weight_brut);
//            $deliveryOrder->kg = floatval($this->weight_brut);
            $deliveryOrder->order_number = $orderNumber;
            $deliveryOrder->save(false);

            return true;

        } else {
            $dp = new TlDeliveryProposal();
            $dp->client_id = $this->client_id;
            $dp->source = TlDeliveryProposal::SOURCE_AUTO_OUTBOUND;
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
     *Выставляем статус груза в зависимости от главного статуса
     **/
    public function updateCargoStatus()
    {
        if($this->status ==  Stock::STATUS_OUTBOUND_NEW){
            $this->cargo_status = OutboundOrder::CARGO_STATUS_NEW;
        } elseif($this->status == Stock::STATUS_OUTBOUND_COMPLETE || $this->status == Stock::STATUS_OUTBOUND_DONE || $this->status == Stock::STATUS_OUTBOUND_DELIVERED){
            $this->cargo_status = OutboundOrder::CARGO_STATUS_DELIVERED;
        } elseif ($this->status == Stock::STATUS_OUTBOUND_SHIPPED || $this->status == Stock::STATUS_OUTBOUND_ON_ROAD){
            $this->cargo_status = OutboundOrder::CARGO_STATUS_ON_ROUTE;
        } elseif ($this->status == Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API){
            if($this->date_left_warehouse){
                $this->cargo_status = OutboundOrder::CARGO_STATUS_ON_ROUTE;
            } else {
                $this->cargo_status = OutboundOrder::CARGO_STATUS_IN_PROCESSING;
            }
        }

        else {
            $this->cargo_status = OutboundOrder::CARGO_STATUS_IN_PROCESSING;
        }
    }

    /**
     * @return string Читабельный текст зоны
     */
    public function getZoneValue($zone = null)
    {
        if(is_null($zone)){
            $zone = $this->zone;
        }
        return ArrayHelper::getValue($this->getZoneArray(), $zone);
    }


    /**
     * @return array Массив с зонами.
     */
    public function getZoneArray()
    {
        return [
            ConstantZone::CATEGORY_A => Yii::t('stock/titles', 'категория А'),
            ConstantZone::CATEGORY_B => Yii::t('stock/titles', 'товары категория Б'),
            ConstantZone::CATEGORY_VV => Yii::t('stock/titles', 'категория ВB'),
            ConstantZone::CATEGORY_RETURN => Yii::t('stock/titles', 'возвраты'),
            ConstantZone::CATEGORY_FUNDS => Yii::t('stock/titles', 'фонды'),
            ConstantZone::CATEGORY_UNADAPTED => Yii::t('stock/titles', 'неадаптированные'),
        ];
    }

    /**
     * @return string Название магазина прибытия.
     */
    public function getToPointValue()
    {
        return TLHelper::getStoreNameById($this->to_point_id);
    }
}

