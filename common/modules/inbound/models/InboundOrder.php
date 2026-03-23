<?php

namespace common\modules\inbound\models;

use common\modules\stock\models\ConstantZone;
use common\modules\stock\models\Stock;
use Yii;
use common\models\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use common\modules\client\models\Client;
use common\modules\store\models\Store;
use common\modules\inbound\models\ConsignmentInboundOrders;

/**
 * This is the model class for table "inbound_orders".
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
 * @property integer $order_type
 * @property integer $delivery_type
 * @property integer $parent_order_number
 * @property integer $consignment_inbound_order_id
 * @property integer $status
 * @property integer $cargo_status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $allocated_qty
 * @property integer $accepted_number_places_qty
 * @property integer $expected_number_places_qty
 * @property integer $zone
 * @property integer $expected_datetime
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $date_confirm
 * @property integer $data_created_on_client
 * @property string  $client_box_barcode
 * @property string  $comments
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class InboundOrder extends ActiveRecord
{
    /*
     * @var integer status
     *
     * */
//    const STATUS_NEW = 1; // Новая накланая, ее еще не начали сканировать
//    const STATUS_IN_SCANNING = 2; // Товары из накладной начали сканировать
    const STATUS_COMPLETE = 3; // Накладная принята, но НЕ отправлено подтверждение по API
//    const STATUS_COMPLETE_BY_API = 4; // Накладная принята, И отправлено подтверждение по API

    const ORDER_TYPE_INBOUND = 1;
    const ORDER_TYPE_RETURN = 2;
	const ORDER_TYPE_ECOMM_RETURN = 4;

    const DELIVERY_TYPE_RPT = 1; // RPT
    const DELIVERY_TYPE_CROSS_DOCK = 2; // CROSS-DOCK
    const DELIVERY_TYPE_CROSS_DOCK_A = 3; // CROSS-DOCK COLINS

    // Статус груза
    const CARGO_STATUS_NEW = 1; //новый
    const CARGO_STATUS_ARRIVED = 2; //прибыл на склад
    const CARGO_STATUS_IN_PROCESSING = 3; //в обработке
    const CARGO_STATUS_PROCESSING_COMPLETE = 4; //обработан



//    const STATUS_PRINTED_PRODUCT_LABELS = 3; // для отсканированных товаров в короб распечатаны ценники
//    const STATUS_PRINTED_BOX_LABELS = 4; // распечатаны этикетки для коробов
//    const STATUS_PACKED = 5; // после того, как этикетки на короба распечатаны, товары переходят в статус упакован
//    const STATUS_SHIPPED_COURIER = 6; // короба с товаром упакованы, этикетки на короба наклеены, и отгружены в курьерскую службу

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inbound_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zone','allocated_qty','delivery_type','data_created_on_client','consignment_inbound_order_id','to_point_id','from_point_id','client_id', 'supplier_id', 'warehouse_id', 'order_type', 'status', 'cargo_status', 'expected_qty', 'accepted_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at, date_confirm'], 'integer'],
            [['client_order_id','order_number'],'string'],
            [['comments','client_box_barcode','parent_order_number','to_point_title','from_point_title','extra_fields'],'string'],
			[['comments'],'string','on'=>'CommentsAdd'],
            [['comments'],'required','on'=>'CommentsAdd'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'comments' => Yii::t('inbound/forms', 'Comments'),
            'zone' => Yii::t('inbound/forms', 'Zone'),
            'delivery_type' => Yii::t('inbound/forms', 'Delivery type'),
            'from_point_title' => Yii::t('inbound/forms', 'from_point_title'),
            'to_point_title' => Yii::t('inbound/forms', 'to_point_title'),
            'parent_order_number' => Yii::t('inbound/forms', 'Party number'),
            'client_box_barcode' => Yii::t('inbound/forms', 'client_box_barcode'),
            'data_created_on_client' => Yii::t('inbound/forms', 'data_created_on_client'),
            'consignment_inbound_order_id' => Yii::t('inbound/forms', 'consignment_inbound_order_id'),
            'from_point_id' => Yii::t('outbound/forms', 'From point id'),
            'to_point_id' => Yii::t('outbound/forms', 'To point id'),
            'id' => Yii::t('inbound/forms', 'ID'),
            'client_id' => Yii::t('inbound/forms', 'Client ID'),
            'supplier_id' => Yii::t('inbound/forms', 'Supplier ID'),
            'warehouse_id' => Yii::t('inbound/forms', 'Warehouse ID'),
            'order_number' => Yii::t('inbound/forms', 'Order Number'),
            'order_type' => Yii::t('inbound/forms', 'Order Type'),
            'status' => Yii::t('inbound/forms', 'Status'),
            'cargo_status' => Yii::t('inbound/forms', 'Cargo Status'),
            'expected_qty' => Yii::t('inbound/forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('inbound/forms', 'Accepted Qty'),
            'allocated_qty' => Yii::t('inbound/forms', 'Allocated Qty'),
            'accepted_number_places_qty' => Yii::t('inbound/forms', 'Accepted Number Places Qty'),
            'expected_number_places_qty' => Yii::t('inbound/forms', 'Expected Number Places Qty'),
            'expected_datetime' => Yii::t('inbound/forms', 'Expected Datetime'),
            'begin_datetime' => Yii::t('inbound/forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('inbound/forms', 'End Datetime'),
            'date_confirm' => Yii::t('inbound/forms', 'Confirmed At'),
            'created_user_id' => Yii::t('inbound/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('inbound/forms', 'Updated User ID'),
            'created_at' => Yii::t('inbound/forms', 'Created At'),
            'updated_at' => Yii::t('inbound/forms', 'Updated At'),
        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public function getStatusArray()
    {
        return [
            Stock::STATUS_NOT_SET => Yii::t('stock/titles', 'Not set'),
            Stock::STATUS_INBOUND_NEW => Yii::t('stock/titles', 'Inbound new'),
            Stock::STATUS_INBOUND_EXPECTED_DELIVERY => Yii::t('stock/titles', 'Expected inbound delivery'),
            Stock::STATUS_INBOUND_SCANNING => Yii::t('stock/titles', 'Scanning(inbound)'),
            Stock::STATUS_INBOUND_SCANNED => Yii::t('stock/titles', 'Scanned(inbound)'),
            Stock::STATUS_INBOUND_OVER_SCANNED => Yii::t('stock/titles', 'Over scanned (inbound)'),
            Stock::STATUS_INBOUND_PLACED => Yii::t('stock/titles', 'Placed (inbound)'),
            Stock::STATUS_INBOUND_ACCEPTED => Yii::t('stock/titles', 'Accepted (inbound)'),
            Stock::STATUS_INBOUND_COMPLETE => Yii::t('stock/titles', 'Complete (inbound)'),
            Stock::STATUS_INBOUND_CONFIRM => Yii::t('stock/titles', 'Confirm (inbound)'),
            Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API => Yii::t('stock/titles', 'Inbound file for API downloaded'),
            Stock::STATUS_INBOUND_SORTED => Yii::t('stock/titles', 'Inbound sorting'),
        ];
    }

    /**
     * @return array Массив с статусами груза.
     */
    public static function getCargoStatusArray()
    {
        return [
            self::CARGO_STATUS_NEW => Yii::t('stock/titles', 'New'),
            self::CARGO_STATUS_ARRIVED => Yii::t('stock/titles', 'Arrived at the warehouse'),
            self::CARGO_STATUS_IN_PROCESSING => Yii::t('stock/titles', 'In processing at the warehouse'),
            self::CARGO_STATUS_PROCESSING_COMPLETE => Yii::t('stock/titles', 'Processing complete'),
        ];
    }


    /**
     * @return array Массив с статусами.
     */
    public function getOrderTypeArray()
    {
        return [
            self::ORDER_TYPE_INBOUND => Yii::t('inbound/titles', 'Inbound'),
            self::ORDER_TYPE_RETURN => Yii::t('inbound/titles', 'Return'),
            self::ORDER_TYPE_ECOMM_RETURN => Yii::t('inbound/titles', 'Возврат B2C ручной'),
        ];
    }
    /**
     * @return string Читабельный статус поста.
     */
    public function getOrderTypeValue($order_type = null)
    {
        if(is_null($order_type)){
            $order_type = $this->order_type;
        }
        return ArrayHelper::getValue($this->getOrderTypeArray(), $order_type);
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
    * Relation has many with InboundOrderItem
    * */
    public function getOrderItems()
    {
        return $this->hasMany(InboundOrderItem::className(), ['inbound_order_id' => 'id']);
    }

    /*
   * Relation has one with ConsignmentOutboundOrder
   * */
    public function getParentOrder()
    {
        return $this->hasOne(ConsignmentInboundOrders::className(), ['id' => 'consignment_inbound_order_id']);
    }

    /*
    * Relation has many with InboundOrderItemProcess
    * */
//    public function getOrderItemProcess()
    public function getOrderItemInStock()
    {
        return $this->hasMany(Stock::className(), ['inbound_order_id' => 'id']);
//        return $this->hasMany(InboundOrderItemProcess::className(), ['inbound_order_id' => 'id']);
    }

    /*
    * Get list inbound orders in status new and in process
    * @return array
    * */
    public static function getNewAndInProcessItems()
    {
        return ArrayHelper::map(self::find()->select('id, order_number')->where(['status'=>[Stock::STATUS_INBOUND_NEW,Stock::STATUS_INBOUND_SCANNING]])->andWhere(['deleted'=>ActiveRecord::NOT_SHOW_DELETED])->asArray()->all(),'id', 'order_number');
    }

    /*
     * Get inbound orders by client id and in status new and in process
     * @return integer $clientID Client id
     * @return array
     * */
    public static function getNewAndInProcessItemByClientID($clientID)
    {
        return ArrayHelper::map(self::find()->select('id, order_number')->where(['status'=>[Stock::STATUS_INBOUND_NEW,Stock::STATUS_INBOUND_SCANNING,Stock::STATUS_INBOUND_SCANNED],'client_id'=>$clientID])->andWhere(['deleted'=>ActiveRecord::NOT_SHOW_DELETED])->asArray()->all(),'id', 'order_number');
    }

    /*
     * Get inbound orders by client id and in status complete
     * @return integer $clientID Client id
     * @return array
     * */
    public static function getCompleteOrderByClientID($clientID)
    {
        return ArrayHelper::map(self::find()->select('id, order_number')->where(['status'=>[Stock::STATUS_INBOUND_CONFIRM,Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API],'client_id'=>$clientID])->andWhere(['deleted'=>ActiveRecord::NOT_SHOW_DELETED])->asArray()->all(),'id', 'order_number');
    }

    /*
    * Get count inbound items by id
    * @return integer $ID inbound order id
    * @return array
    * */
    public static function getCountItemByID($ID)
    {
        $m = self::findOne($ID);
        return ($m ? $m->accepted_qty : '0');
    }

    /*
   * Relation has one with Client
   * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
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
  * Get count order accepted
  * */
    public function recalculateOrderItems()
    {
        if($items = $this->orderItems){
            $expectedQty = 0;
            $acceptedQty = 0;
            foreach ($items as $item){
                $expectedQty += $item->expected_qty;
                $acceptedQty += $item->accepted_qty;
            }

            $this->expected_qty = $expectedQty;
            $this->accepted_qty = $acceptedQty;
            $this->save(false);
        }
    }

    /*
     * Create stock records based on Inbound Items
     * USED ONLY FOR TEST
     **/
    public function createStock(){

            $InboundOrderItems = InboundOrderItem::find()
                ->where(['inbound_order_id' => $this->id])
                ->all();

            if (!empty($InboundOrderItems) && is_array($InboundOrderItems)) {
                foreach ($InboundOrderItems as $inbound) {
                    if (!(Stock::find()->where([
                        'client_id' => $this->client_id,
                        'inbound_order_id' => $this->id,
                        'product_barcode' => $inbound->product_barcode,
                    ])->exists())
                    ) {

                        for ($i = 0; $i < $inbound->expected_qty; $i++) {
                            // STOCK

                            $stock = new Stock();
                            $stock->client_id = $this->client_id;
                            $stock->inbound_order_id = $this->id;
                            $stock->product_barcode = $inbound->product_barcode;
                            $stock->product_model = $inbound->product_model;
                            $stock->status = Stock::STATUS_INBOUND_SORTING;
                            $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
                            $stock->save(false);

                        }
                    }
                }
            }
    }

    /*
     *
     * */
    public function getOrderItemsSortedByDiff()
    {
        return $this->getOrderItems()->select('*,(expected_qty - accepted_qty) as order_by')->orderBy(new Expression('order_by!=0 DESC'))->asArray()->all();
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
}