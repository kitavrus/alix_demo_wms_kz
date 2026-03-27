<?php

namespace common\modules\stock\models;

use common\managers\base\AllocateManager;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\product\models\ProductBarcodes;
use Yii;
use yii\helpers\ArrayHelper;
use common\modules\inbound\models\InboundOrder;
use yii\helpers\VarDumper;
use common\modules\client\models\Client;

/**
 * This is the model class for table "stock".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $inbound_order_id
 * @property integer $inbound_order_item_id
 * @property integer $consignment_inbound_id
 * @property integer $outbound_order_id
 * @property integer $outbound_order_item_id
 * @property integer $consignment_outbound_id
 * @property integer $outbound_picking_list_id
 * @property string  $outbound_picking_list_barcode
 * @property integer $warehouse_id
 * @property integer $movement_id
 * @property integer $movement_item_id
 * @property integer $zone
 * @property integer $product_id
 * @property string $outbound_order_number
 * @property string $inbound_order_number
 * @property string $inventory_secondary_address
 * @property string $kpi_value
 * @property integer $status_lost
 * @property string $product_name
 * @property string $product_barcode
 * @property string $product_model
 * @property string $product_sku
 * @property integer $is_product_type
 * @property string $box_barcode
 * @property string $box_size_barcode
 * @property string $box_size_m3
 * @property string $box_kg
 * @property integer $condition_type
 * @property integer $status
 * @property integer $pick_list_status
 * @property integer $stock_availability
 * @property integer $status_availability
 * @property string $inventory_primary_address
 * @property integer $status_inventory
 * @property integer $inventory_id
 * @property string $primary_address
 * @property string $secondary_address
 * @property integer $address_pallet_qty
 * @property integer $address_sort_order
 * @property integer $scan_in_datetime
 * @property integer $scan_out_datetime
 * @property string  $inbound_client_box
 * @property string  $system_status_description
 * @property string  $system_status
 * @property string $field_extra1
 * @property string $field_extra2
 * @property string $field_extra3
 * @property string $field_extra4
 * @property string $field_extra5
 * @property string $our_product_barcode
 * @property string $bind_qr_code
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class Stock extends \common\models\ActiveRecord
{
    public $product_barcode_count;

    /*
     * @var status
     * */
    const STATUS_NOT_SET = 0;// Статус не определен

    const STATUS_INBOUND_NEW = 1; // Товары из приходной накладной ЗАГРУЗИЛИ В СИСТЕМУ но ничего с ними не делали (на склад они к нам не прибыли)
    const STATUS_INBOUND_EXPECTED_DELIVERY = 2; // ОЖИДАЕТСЯ ПОСТАВКА
    const STATUS_INBOUND_SCANNING = 3; // НАЧАЛИ СКАНИРОВАТЬ товары из приходной накладной
    const STATUS_INBOUND_SCANNED = 4; // Товар ОТСКАНИРОВАН из приходной накладной
    const STATUS_INBOUND_OVER_SCANNED = 5; // ЛИШНИЙ ОТСКАНИРОВАНЫЙ товар в приходной накладной
    const STATUS_INBOUND_PLACED = 6; // Товар из приходной накладной РАЗМЕСТИЛИ но не приняли на склад
    const STATUS_INBOUND_ACCEPTED = 7; // Товар из приходной накладной ПРИНЯТ на склад. но не подвержден по API
    const STATUS_INBOUND_COMPLETE = 8; //  ЗАКАЗ ВЫПОЛНЕН (принят на склад, (все посканировали и разместили) данные отправлены по API клиенты (если это необходимо) )
    const STATUS_INBOUND_CONFIRM = 9; //  ЗАКАЗ ПРИНЯТ на СКЛАД
    const STATUS_INBOUND_PREPARED_DATA_FOR_API = 10; // Создали файл для отгрузки по API к клиенту

    const STATUS_OUTBOUND_NEW = 11;
    const STATUS_OUTBOUND_FULL_RESERVED = 12; //  ПОЛНОСТЬЮ ЗАРЕЗЕРВИРОВАНА
    const STATUS_OUTBOUND_RESERVING = 13; //  В ПРОЦЕССЕ ЗАРЕЗЕРВИРОВАНИЯ
    const STATUS_OUTBOUND_PART_RESERVED = 14; //  ЧАСТИЧНО ЗАРЕЗЕРВИРОВАНА
    const STATUS_OUTBOUND_PRINTED_PICKING_LIST = 15; //  Напечатали лист сборки
    const STATUS_OUTBOUND_PICKING = 16; //  СОБИРАТСЯ
    const STATUS_OUTBOUND_PICKED = 17;  //  СОБРАН

    const STATUS_OUTBOUND_SCANNING = 18; //  СКАНИРУЕТСЯ
    const STATUS_OUTBOUND_SCANNED = 19;  //  ОТСКАНИРОВАН

    const STATUS_OUTBOUND_SORTING = 20; //  СОРТИРУЕТСЯ
    const STATUS_OUTBOUND_SORTED = 21;  //  РАССОРТИРОВАН

    const STATUS_OUTBOUND_PACKING = 22; //  УПАКОВЫВАЕТСЯ
    const STATUS_OUTBOUND_PACKED = 23;  //  УПАКОВАН

    const STATUS_OUTBOUND_SHIPPING = 24;  // ОТГРУЗАЕТСЯ со склада
    const STATUS_OUTBOUND_SHIPPED = 25;  // ОТГРУЖЕН со склада (погрузили в машину). С этого момента товара на складе больше нет

    const STATUS_OUTBOUND_COMPLETE = 26; //  ЗАКАЗ ВЫПОЛНЕН (заказ собран и отгружен со склада, данные отправлены по API клиенты (если это необходимо) )

    const STATUS_OUTBOUND_PREPARED_DATA_FOR_API = 27; // Создали файл для отгрузки по API к клиенту

    const STATUS_OUTBOUND_ON_ROAD = 28; //  В ПУТИ
    const STATUS_OUTBOUND_DELIVERED = 29; //  ДОСТАВЛЕН
    const STATUS_OUTBOUND_DONE = 30; //Нигде не используется!!!  ТОВАР ДОСТАВЛЕН ВСЕ В ПОРЯДКЕ

    const STATUS_OUTBOUND_CANCEL = 31;   // ОТМЕНЕН
    const STATUS_OUTBOUND_PRINT_BOX_LABEL = 32;   // Распечатали этикетки на короба

    const STATUS_INBOUND_CREATED_ON_CLIENT_SIDE = 33; // Создается на стороне клиента

    const STATUS_OUTBOUND_PRINTING_BOX_LABEL = 34;   // Распечатали этикетки для одного из сборочных листов

    const STATUS_CROSS_DOCK_NEW = 35; // Новая накладная
    const STATUS_CROSS_DOCK_PRINTED_PICKING_LIST = 36; // Распечатали лист сборки
    const STATUS_CROSS_DOCK_COMPLETE = 37; // Накладная собрана

    const STATUS_INBOUND_SORTED = 38; // сортируем приходный заказ для колинс
    const STATUS_INBOUND_SORTING = 39; // сортируем приходный заказ для колинс

    const STATUS_CROSS_DOCK_SCANNING = 40; // Крос-док СКАНИРУЕТСЯ
    const STATUS_CROSS_DOCK_SCANNED = 41; // Крос-док ОТСКАНИРОВАН
    const STATUS_INBOUND_CANCEL =  42; // Приходная накладная отменена
    const STATUS_INBOUND_DONE = 43; //для
    const STATUS_INBOUND_CLOSE = 44; //накладная принята

    // LAST 30

    /*@var status availability
     **/
//    const STATUS_AVAILABILITY_NO = 0; // НЕ ДОСТУПНО для резервирования
//    const STATUS_AVAILABILITY_YES = 1; // ДОСТУПНО для резервирования
//    const STATUS_AVAILABILITY_RESERVED = 2; // ЗАРЕЗЕРВИРОВАН
//    const STATUS_AVAILABILITY_BLOCKED = 3; // ЗАБЛОКИРОВАН недоступен для резервирования. Примеры, товар поврежден или потерян

    const STATUS_AVAILABILITY_NOT_SET = 0; // Not set
    const STATUS_AVAILABILITY_NO = 1; // НЕ ДОСТУПНО для резервирования
    const STATUS_AVAILABILITY_YES = 2; // ДОСТУПНО для резервирования
    const STATUS_AVAILABILITY_RESERVED = 3; // ЗАРЕЗЕРВИРОВАН
    const STATUS_AVAILABILITY_BLOCKED = 4; // ЗАБЛОКИРОВАН недоступен для резервирования. Примеры, товар поврежден или потерян
    const STATUS_AVAILABILITY_TEMPORARILY_RESERVED = 5; // Частично зарезервирована. Это нужно для Солинс


   /*
    * @var condition type
    *
    **/
    const CONDITION_TYPE_NOT_SET = 0; //не определен
    const CONDITION_TYPE_UNDAMAGED = 1; //Неповрежденный
    const CONDITION_TYPE_PARTIAL_DAMAGED = 2; //частично поврежден
    const CONDITION_TYPE_FULL_DAMAGED = 3; //полностью поврежден

    /**
     * status lost
     */
    const STATUS_LOST_NOT_SET = 0;
    const STATUS_LOST_AVAILABLE = 1;
    const STATUS_LOST_PARTIAL = 2;
    const STATUS_LOST_FULL = 3;

    /*
     * Product Box type: Box lot or Return
     * */
    const IS_PRODUCT_TYPE_LOT = 0;
    const IS_PRODUCT_TYPE_LOT_BOX = 1;
    const IS_PRODUCT_TYPE_RETURN = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [ // 'movement_item_id','movement_id',
            [['scan_in_datetime','scan_out_datetime','pick_list_status','zone','address_sort_order','inbound_order_item_id','consignment_inbound_id','consignment_outbound_id','outbound_order_item_id','client_id','outbound_picking_list_id','inbound_order_id', 'outbound_order_id', 'warehouse_id', 'product_id', 'condition_type', 'status', 'status_availability', 'status_lost', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_name', 'product_model', 'product_sku'], 'string', 'max' => 128],
            [['product_barcode', 'box_barcode', 'our_product_barcode', 'bind_qr_code'], 'string', 'max' => 54],
            [['primary_address', 'secondary_address','inventory_primary_address'], 'string', 'max' => 25],
            [['box_size_m3','box_size_barcode','outbound_picking_list_barcode','inbound_client_box','system_status'], 'string', 'max' => 32],
            [['system_status_description'], 'string'],
            [['field_extra1','field_extra2','field_extra3','field_extra4','field_extra5'], 'string'],
            [['address_pallet_qty'], 'integer','min'=>1,'max'=>3],
            [['is_product_type'], 'integer','min'=>0,'max'=>3],
			[['our_product_barcode', 'bind_qr_code'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('stock/forms', 'ID'),
            'client_id' => Yii::t('stock/forms', 'Client ID'),
            'inbound_order_id' => Yii::t('stock/forms', 'Inbound Order ID'),
            'consignment_inbound_id' => Yii::t('stock/forms', 'Consignment inbound'),
            'inbound_order_item_id' => Yii::t('stock/forms', 'Inbound order item id'),
            'outbound_order_id' => Yii::t('stock/forms', 'Outbound Order ID'),
            'outbound_order_item_id' => Yii::t('stock/forms', 'Outbound order item id'),
            'consignment_outbound_id' => Yii::t('stock/forms', 'Consignment outbound'),
            'outbound_picking_list_id' => Yii::t('stock/forms', 'Outbound Picking list ID'),
            'warehouse_id' => Yii::t('stock/forms', 'Warehouse ID'),
            'product_id' => Yii::t('stock/forms', 'Product ID'),
            'product_name' => Yii::t('stock/forms', 'Product name'),
            'product_barcode' => Yii::t('stock/forms', 'Product barcode'),
            'product_model' => Yii::t('stock/forms', 'Product model'),
            'product_sku' => Yii::t('stock/forms', 'Product Sku'),
            'box_barcode' => Yii::t('stock/forms', 'Box Barcode'),
            'box_size_m3' => Yii::t('stock/forms', 'Box Volume'),
            'box_kg' => Yii::t('stock/forms', 'Box kg'),
            'condition_type' => Yii::t('stock/forms', 'Condition type'),
            'status' => Yii::t('stock/forms', 'Status'),
            'status_availability' => Yii::t('stock/forms', 'Status availability'),
            'status_lost' => Yii::t('stock/forms', 'Status lost'),
            'primary_address' => Yii::t('stock/forms', 'Primary address'),
            'secondary_address' => Yii::t('stock/forms', 'Secondary address'),
            'address_sort_order' => Yii::t('stock/forms', 'Sort order Secondary address'),
            'inbound_client_box' => Yii::t('stock/forms', 'Client inbound box'),
            'our_product_barcode' => Yii::t('stock/forms', 'Наш ШК товара'),
            'bind_qr_code' => Yii::t('stock/forms', 'QR Code'),
            'created_user_id' => Yii::t('stock/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('stock/forms', 'Updated User ID'),
            'created_at' => Yii::t('stock/forms', 'Created At'),
            'updated_at' => Yii::t('stock/forms', 'Updated At'),
            'deleted' => Yii::t('stock/forms', 'Deleted'),
			'our_product_barcode' => Yii::t('stock/forms', 'Наш ШК товара'),
			'bind_qr_code' => Yii::t('stock/forms', 'QR Code'),
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
            Stock::STATUS_OUTBOUND_CANCEL => Yii::t('stock/titles', 'Cancel'),
            Stock::STATUS_OUTBOUND_NEW => Yii::t('stock/titles', 'Outbound new'),
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
            Stock::STATUS_OUTBOUND_SCANNING => Yii::t('stock/titles', 'Scanning (outbound)'),//один
            Stock::STATUS_OUTBOUND_SCANNED => Yii::t('stock/titles', 'Scanned (outbound)'),//один
            Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST => Yii::t('stock/titles', 'Printed picking list'),//разные
            Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API => Yii::t('stock/titles', 'Outbound file for API downloaded'),
            Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL => Yii::t('stock/titles', 'Print box label'), //выделить ярким цветом  напечатали
            Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL => Yii::t('stock/titles', 'Printing box label'), //выделить ярким цветом  печатаются
            Stock::STATUS_OUTBOUND_COMPLETE => Yii::t('stock/titles', 'Complete (outbound)'),
            Stock::STATUS_OUTBOUND_DONE => Yii::t('stock/titles', 'Выполнен'),
            Stock::STATUS_CROSS_DOCK_NEW => Yii::t('stock/titles', 'New (cross-dock)'),
            Stock::STATUS_CROSS_DOCK_COMPLETE => Yii::t('stock/titles', 'Complete (cross-dock)'),
            Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST => Yii::t('stock/titles', 'Printed picking list (cross-dock)'),
            Stock::STATUS_INBOUND_SORTED => Yii::t('stock/titles', 'Просортирован'),
            Stock::STATUS_INBOUND_SORTING => Yii::t('stock/titles', 'Сортируется'),
        ];
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
     * Availability status array
     * return mixed
     **/
    public function getAvailabilityStatusArray()
    {
        $data = [
            self::STATUS_AVAILABILITY_NOT_SET => Yii::t('stock/titles', 'Not set'),
            self::STATUS_AVAILABILITY_NO => Yii::t('stock/titles', 'Not available'),
            self::STATUS_AVAILABILITY_YES => Yii::t('stock/titles', 'Available'),
            self::STATUS_AVAILABILITY_RESERVED => Yii::t('stock/titles', 'Reserved'),
            self::STATUS_AVAILABILITY_BLOCKED => Yii::t('stock/titles', 'Blocked'),
        ];

        return $data;
    }

    /**
     * @return string Читабельный статус.
     */
    public function getAvailabilityStatusValue($status_availability=null)
    {
        if(is_null($status_availability)){
            $status_availability = $this->status_availability;
        }
        return ArrayHelper::getValue($this->getAvailabilityStatusArray(), $status_availability);
    }

    /*
    * Availability status array
    * return mixed
    **/
    public function getConditionTypeArray()
    {
        $data = [
//            self::CONDITION_TYPE_NOT_SET => Yii::t('stock/titles', 'Not set'),
            self::CONDITION_TYPE_UNDAMAGED => Yii::t('stock/titles', 'Неповрежденный'),
            self::CONDITION_TYPE_PARTIAL_DAMAGED => Yii::t('stock/titles', 'Частично поврежден'),
            self::CONDITION_TYPE_FULL_DAMAGED => Yii::t('stock/titles', 'Полность поврежден'),
        ];

        return $data;
    }

    /**
     * @return string Читабельный статус.
     */
    public function getConditionTypeValue($condition_type=null)
    {
        if(is_null($condition_type)){
            $condition_type = $this->condition_type;
        }
        return ArrayHelper::getValue($this->getConditionTypeArray(), $condition_type);
    }

    /*
   * Availability status array
   * return mixed
   **/
    public function getLostStatusArray()
    {
        $data = [
            self::CONDITION_TYPE_NOT_SET => Yii::t('stock/titles', 'Not set'),
            self::STATUS_LOST_AVAILABLE => Yii::t('stock/titles', 'Available'),
            self::STATUS_LOST_PARTIAL => Yii::t('stock/titles', 'Partial lost'),
            self::STATUS_LOST_FULL => Yii::t('stock/titles', 'Full lost'),
        ];

        return $data;
    }

    /**
     * @return string Читабельный статус.
     */
    public function getLostStatusValue($status_lost=null)
    {
        if(is_null($status_lost)){
            $status_lost = $this->status_lost;
        }
        return ArrayHelper::getValue($this->getLostStatusArray(), $status_lost);
    }

    /*
     * Find and Update
     * @param array $attributes
     * @param string $condition
     * @param array $params
     * */
    public static function findAndUpdate($attributes,$condition,$params = [])
    {
        $q = Stock::find();

        if(!empty($params)) {
            $q->where($condition,$params);
        } else {
            $q->where($condition);
        }

        if($s = $q->one()) {
            $s->setAttributes($attributes);
            $s->save(false);
        }

        return $s;
    }

    /*
     * Find and set status
     * @param integer $$inbound_order_id
     * @param string $product_barcode
     * @param string $box_barcode
     * @param string $product_model
     * */
    public static function setStatusInboundScannedValue($inbound_order_id,$product_barcode,$box_barcode,$product_model = '')
    {
        $client_id = 0;
        if($inbound = InboundOrder::findOne($inbound_order_id)) {
            $client_id = $inbound->client_id;
        }

        $attributes = [
            'status'=>self::STATUS_INBOUND_SCANNED,
            'primary_address'=>$box_barcode,
        ];
        $condition = 'client_id = :client_id AND (status = :status1 OR status = :status2) AND inbound_order_id = :inbound_order_id AND product_barcode = :product_barcode';
        $params = [
            ':inbound_order_id'=>$inbound_order_id,
            ':product_barcode'=>$product_barcode,
            ':status1'=>self::STATUS_INBOUND_NEW,
            ':status2'=>self::STATUS_INBOUND_SCANNING,
            ':client_id'=>$client_id
        ];

        if(!($s = Stock::find()->where($condition,$params)->one())) {
            $s = new Stock();
            $attributes = [
                'client_id'=>$client_id,
                'inbound_order_id'=>$inbound_order_id,
                'product_barcode'=>$product_barcode,
                'product_model'=>$product_model,
                'primary_address'=>$box_barcode,
//                'status'=>self::STATUS_INBOUND_OVER_SCANNED
                'status'=>self::STATUS_INBOUND_SCANNED
            ];
        }

        $s->setAttributes($attributes);
        $s->save(false);


        return $s;


//        return self::findAndUpdate($attributes,$condition,$params);
    }

    /*
     * Find and set status
     * @param integer $$inbound_order_id
     * @param string $product_barcode
     * @param string $box_barcode
     * @param string $product_model
     * */
    public static function setStatusInboundScannedValueByConsignmentOrder($client_id, $consignment_order_id, $product_barcode, $box_barcode,$product_model = '')
    {
       $s = false;
        if(!$product_model){
           if($product = ProductBarcodes::getProductByBarcode($client_id, $product_barcode)) {
               $product_model = $product->model;
           }
        }

        if($cio = ConsignmentInboundOrders::findOne($consignment_order_id)){
            // ищем ID всех Inbound Orders этой партии
            $inboundOrdersIDs = InboundOrder::find()->select('id')
                ->andWhere(['consignment_inbound_order_id'=>$consignment_order_id, 'client_id' => $client_id])
                ->asArray()
                ->column();

            $attributes = [
                'status' => self::STATUS_INBOUND_SCANNED,
                'primary_address'=>$box_barcode,
                'product_model' =>$product_model,
            ];

            // находим этот товар у нас в стоке
            if(!($s = Stock::find()
                ->andWhere([
                    'status'=>[
//                        self::STATUS_INBOUND_NEW,
//                        self::STATUS_INBOUND_SCANNING,
//                        self::STATUS_INBOUND_SORTED,
                        self::STATUS_INBOUND_SORTING,
//                        self::STATUS_OUTBOUND_PICKED,
                   ],
                    'inbound_order_id' => $inboundOrdersIDs,
                    'product_barcode' => $product_barcode,
                ])
                ->one())) {

                //если нету то создаем специальный Inbound Order для лишних товаров и ложим его туда
                if(!$io = InboundOrder::find()->andWhere(['order_number' => $cio->party_number.'-overscanned', 'consignment_inbound_order_id' => $cio->id, 'client_id' => $client_id])->one()){
                    $io = new InboundOrder();
                    $io->client_id = $client_id;
                    $io->order_number = $cio->party_number.'-overscanned';
                    $io->parent_order_number = $cio->party_number;
                    $io->status = Stock::STATUS_INBOUND_OVER_SCANNED;
                    $io->consignment_inbound_order_id = $cio->id;
                    $io->save(false);

                }
                //ищем item для этого товара, если нету то создаем
                if(!($ioi = InboundOrderItem::find()->andWhere(['inbound_order_id'=>$io->id,'product_barcode' => $product_barcode])->one())) //, 'status' => Stock::STATUS_INBOUND_OVER_SCANNED,
                {
                    $ioi = new InboundOrderItem();
                    $ioi->product_model = $product_model;
                    $ioi->inbound_order_id = $io->id;
                    $ioi->product_barcode = $product_barcode;
                    $ioi->box_barcode = $box_barcode;
                    $ioi->status = Stock::STATUS_INBOUND_OVER_SCANNED;
                    $ioi->accepted_qty = 0;
                }

                $ioi->accepted_qty += 1;
                $ioi->save(false);
                $io->recalculateOrderItems();

                $s = new Stock();
                $attributes = [
                    'client_id'=>$client_id,
                    'inbound_order_id'=>$io->id,
                    'product_barcode'=>$product_barcode,
                    'product_model'=>$product_model,
                    'primary_address'=>$box_barcode,
                    'status'=>self::STATUS_INBOUND_OVER_SCANNED
//                    'status'=>self::STATUS_INBOUND_SCANNED
                ];
            }

            $s->setAttributes($attributes);
            $s->save(false);
        }

        return $s;


//        return self::findAndUpdate($attributes,$condition,$params);
    }

    /*
     * Allocate outbound order
     * @param integer $outbound_order_id
     * */
    public static function AllocateByOutboundOrderId($outbound_order_id,$address = [])
    {
//        $expected_qty = 0; // test
        if($oo = OutboundOrder::findOne($outbound_order_id)) {
            $allocate_qty = 0;
            if($items = $oo->getOrderItems()->all()) {
                foreach($items as $itemLine) {


                    $itemLine->allocated_qty = 0;
                    $itemLine->status = Stock::STATUS_OUTBOUND_RESERVING;
                    //$itemLine->save(false);

//                    $inStocks = Stock::find()->andWhere([
//                                        'client_id' => $oo->client_id,
//                                        'product_barcode' =>$itemLine->product_barcode,
//                                        'status_availability'=>self::STATUS_AVAILABILITY_YES]
//                                )
//                                ->andFilterWhere(['secondary_address'=>$address])
//                                ->orderBy('address_sort_order')
//                                ->limit($itemLine->expected_qty)
//                                ->all();

                    $inStocks = Stock::find()->andWhere([
                                        'id' => AllocateManager::strategyClearEmptyBox($itemLine->product_barcode,$itemLine->expected_qty,$oo->client_id),
                                ])
                                ->orderBy('address_sort_order')
                                ->all();



                    if ($inStocks) {
                        foreach($inStocks as $stockLine) {
                            // ORDER ITEM
                            $itemLine->allocated_qty +=1;
                            $allocate_qty++;
                            //STOCK
                            $stockLine->outbound_order_id = $oo->id;
                            $stockLine->outbound_order_item_id = $itemLine->id;

                            $stockLine->status = self::STATUS_OUTBOUND_FULL_RESERVED;
                            $stockLine->status_availability = self::STATUS_AVAILABILITY_RESERVED;
                            $stockLine->save(false);
                        }
                    }


                    $itemLine->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

                    if( $itemLine->allocated_qty == $itemLine->expected_qty ) {
                        $itemLine->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
                    }

                    $itemLine->save(false);
                }
            }

            $oo->allocated_qty = $allocate_qty;
            $oo->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

            if( $oo->allocated_qty == $oo->expected_qty ) {
                $oo->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
            }

            $oo->save(false);

            // Стоит ли проверять статус для все партии
            if( $consignmentModel = ConsignmentOutboundOrder::findOne(['client_id'=>$oo->client_id,'party_number'=>$oo->parent_order_number]) ){
                $consignmentModel->allocated_qty += $allocate_qty;

                if($consignmentModel->status != Stock::STATUS_OUTBOUND_PART_RESERVED && ($oo->status == Stock::STATUS_OUTBOUND_PART_RESERVED) ) {
                    $consignmentModel->status = Stock::STATUS_OUTBOUND_PART_RESERVED;
                } else {
                    $consignmentModel->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
                }

                $consignmentModel->save(false);
            }
        }
    }

    /*
     * Allocate outbound order
     * @param integer $outbound_order_id
     * */
    public static function allocateAnyBarcodeByOutboundOrderId($outbound_order_id,$address = [])
    {
//        $expected_qty = 0; // test
        if($oo = OutboundOrder::findOne($outbound_order_id)) {
            $allocatedQty = 0;
            $inboundItemsGroupBySkuIDs = [];
            $expectedQty = 0;
            if($items = $oo->getOrderItems()->all()) {

                foreach($items as $itemLine) {
                    $inboundItemsGroupBySkuIDs[$itemLine->field_extra1][] = $itemLine;
                }

//                echo $oo->order_number.' '.$oo->parent_order_number;
//                VarDumper::dump($inboundItemsGroupBySkuIDs,10,true); // 223137188
//                die;

                foreach($inboundItemsGroupBySkuIDs as $skuID=>$inboundItems) {
                    $currentSkuIDToLines = [];
                    foreach ($inboundItems as $i=>$item) {
                        if($i == 0) {
                            $expectedQty += $item->expected_qty;
                            $currentSkuIDToLines['exp'] = $item->expected_qty;
                            $currentSkuIDToLines['res'] = 0;
                        }
                        $currentSkuIDToLines['lines'][$item->id] = 0;

//                        echo $oo->order_number.' '.$oo->parent_order_number;
//                        VarDumper::dump($currentExpectedSkuQty,10,true); // 223137188
//                        die;
                        $item->expected_qty -= $currentSkuIDToLines['res'];
                        $item->allocated_qty = 0;
                        $item->status = Stock::STATUS_OUTBOUND_RESERVING;

//                        $inStocksQuery = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBox($item->product_barcode,$item->expected_qty,$oo->client_id)]); // OLD
//$inStocksQuery = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBox($skuID,$item->expected_qty,$oo->client_id)]);
$inStocksQuery = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBoxByProduct($item->product_barcode,$item->expected_qty,$oo->client_id)]);
						
                        $inStocks = $inStocksQuery->orderBy('address_sort_order')->all();
                        if ($inStocks) {
                            foreach($inStocks as $stockLine) {
                                // ORDER ITEM
                                $item->allocated_qty += 1;
                                $currentSkuIDToLines['res'] += 1;
                                $currentSkuIDToLines['lines'][$item->id] += 1;
                                $allocatedQty++;
                                // STOCK
                                $stockLine->outbound_order_id = $oo->id;
                                $stockLine->outbound_order_item_id = $item->id;

                                $stockLine->status = self::STATUS_OUTBOUND_FULL_RESERVED;
                                $stockLine->status_availability = self::STATUS_AVAILABILITY_RESERVED;
                                $stockLine->save(false);

                            }
                        }

                        $item->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

                        if( $item->allocated_qty == $item->expected_qty ) {
                            $item->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
                        }
                        $item->save(false);
                    }

                    $linesCount = count($currentSkuIDToLines['lines']);
                    $linesToDeleted = 0;
                    foreach($currentSkuIDToLines['lines'] as $lineID=>$reservedByLine) {
                        if(empty($reservedByLine) && $linesCount != 1 && $linesToDeleted != ($linesCount-1)) {
                            $linesToDeleted += 1;
                            OutboundOrderItem::deleteAll(['id'=>$lineID]);
                        }
                    }
                }

                $oo->expected_qty = $expectedQty;
                $oo->allocated_qty = $allocatedQty;
                $oo->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

                if( $oo->allocated_qty == $oo->expected_qty ) {
                    $oo->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
                }
//
                $oo->save(false);

                // Стоит ли проверять статус для все партии
                if( $consignmentModel = ConsignmentOutboundOrder::findOne(['client_id'=>$oo->client_id,'party_number'=>$oo->parent_order_number]) ){
                    $consignmentModel->allocated_qty += $allocatedQty;

                    if($consignmentModel->status != Stock::STATUS_OUTBOUND_PART_RESERVED && ($oo->status == Stock::STATUS_OUTBOUND_PART_RESERVED) ) {
                        $consignmentModel->status = Stock::STATUS_OUTBOUND_PART_RESERVED;
                    } else {
                        $consignmentModel->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
                    }
                    $consignmentModel->save(false);
                }

            }

//            echo $oo->order_number.' '.$oo->parent_order_number;
//            VarDumper::dump($expectedQty,10,true); // 223137188
//                die;

//            VarDumper::dump($inboundItemsGroupBySkuID,10,true); // 223137188
//            die('YYYYYYYYYYYYYYYYYY');
            return true;

            if($items = $oo->getOrderItems()->all()) {
//                VarDumper::dump($items,10,true); // 223137188
//                die('YYYYYYYYYYYYYYYYYY');
                foreach($items as $itemLine) {

                    $itemLine->allocated_qty = 0;
                    $itemLine->status = Stock::STATUS_OUTBOUND_RESERVING;

//                    $inStocks = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBox($itemLine->product_barcode,$itemLine->expected_qty,$oo->client_id)])
//                                ->orderBy('address_sort_order')
//                                ->all();

                    $inStocksQuery = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBox($itemLine->product_barcode,$itemLine->expected_qty,$oo->client_id)]);
                    $inStocksCountQuery = clone $inStocksQuery;
                    $inStocksCount = $inStocksCountQuery->count();
                    if($inStocksCount == $itemLine->allocated_qty) {

                        $inStocks = $inStocksQuery->orderBy('address_sort_order')->all();
                        if ($inStocks) {
                            foreach($inStocks as $stockLine) {
                                // ORDER ITEM
                                $itemLine->allocated_qty +=1;
                                $allocate_qty++;
                                //STOCK
                                $stockLine->outbound_order_id = $oo->id;
                                $stockLine->outbound_order_item_id = $itemLine->id;

                                $stockLine->status = self::STATUS_OUTBOUND_FULL_RESERVED;
                                $stockLine->status_availability = self::STATUS_AVAILABILITY_RESERVED;
                                $stockLine->save(false);
                                $skuIDs[$itemLine->field_extra1] +=1;
                            }
                        }

                        $itemLine->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

                        if( $itemLine->allocated_qty == $itemLine->expected_qty ) {
                            $itemLine->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
                        }
                        $itemLine->save(false);

                    } else {
//                        $itemLine->delete();

                    }


                }
            }

            $oo->allocated_qty = $allocate_qty;
            $oo->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

            if( $oo->allocated_qty == $oo->expected_qty ) {
                $oo->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
            }

//            $oo->save(false);

            // Стоит ли проверять статус для все партии
            if( $consignmentModel = ConsignmentOutboundOrder::findOne(['client_id'=>$oo->client_id,'party_number'=>$oo->parent_order_number]) ){
                $consignmentModel->allocated_qty += $allocate_qty;

                if($consignmentModel->status != Stock::STATUS_OUTBOUND_PART_RESERVED && ($oo->status == Stock::STATUS_OUTBOUND_PART_RESERVED) ) {
                    $consignmentModel->status = Stock::STATUS_OUTBOUND_PART_RESERVED;
                } else {
                    $consignmentModel->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
                }

//                $consignmentModel->save(false);
            }
        }
    }





    /*
    * Re Allocate outbound order
    * @param integer $outbound_order_id
    * */
    public static function resetByOutboundOrderId($outbound_order_id)
    {
        if ($outboundOrder = OutboundOrder::findOne($outbound_order_id)) {

            // TODO Доделать !!!!!
            //S: Reset
            OutboundOrder::updateAll(['data_created_on_client' => '',  'accepted_qty' => '0', 'allocated_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_NEW,'cargo_status'=>OutboundOrder::CARGO_STATUS_NEW], ['id' => $outboundOrder->id]);
            ConsignmentOutboundOrder::updateAll([ 'accepted_qty' => '0', 'allocated_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_NEW], ['id' =>  $outboundOrder->consignment_outbound_order_id]);
            OutboundOrderItem::updateAll(['accepted_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_NEW], ['outbound_order_id' => $outboundOrder->id]);
            OutboundPickingLists::deleteAll(['outbound_order_id' => $outboundOrder->id]);
            Stock::updateAll([
                'box_barcode' => '',
                'outbound_order_id' => '0',
                'outbound_picking_list_id' => '0',
                'outbound_picking_list_barcode' => '',
                'status' => Stock::STATUS_INBOUND_NEW,
//                'status' => Stock::STATUS_NOT_SET,
                'status_availability' => Stock::STATUS_AVAILABILITY_YES
            ], ['outbound_order_id' => $outboundOrder->id]);
            //E: Reset
        }

    }

    /*
     * Reset allocated product in stock by  consignment outbound order
     * @param integer $d
     * @return boolean if success return true else false
     * */
    public static function resetAllocatedProductByConsignmentOutboundId($id)
    {
        if($c = ConsignmentOutboundOrder::findOne($id)) {
            $outboundAll = OutboundOrder::find()->where(['consignment_outbound_order_id'=>$c->id])->all();
            if($outboundAll) {
                foreach($outboundAll as $outbound) {
                    $outbound->allocated_qty = 0;
                    $outbound->status = Stock::STATUS_OUTBOUND_NEW;
                    OutboundOrderItem::updateAll([
                        'allocated_qty'=>0,
                    ],[
                        'outbound_order_id'=>$outbound->id
                    ]);
                    Stock::updateAll([
                        'outbound_order_id'=>0,
                        'status'=>Stock::STATUS_OUTBOUND_NEW,
                        'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
                    ],[
                        'outbound_order_id'=>$outbound->id
                    ]);
                    $outbound->save(false);
                }
            }
            $c->allocated_qty = 0;
            $c->save(false);
        }
        return false;
    }

    /*
     * Relation has one with Outbound Order
     **/
    public function getOutboundOrder()
    {
        return $this->hasOne(OutboundOrder::className(), ['id' => 'outbound_order_id']);
    }

    /*
     * Relation has one with Inbound Order
     **/
    public function getInboundOrder()
    {
        return $this->hasOne(InboundOrder::className(), ['id' => 'inbound_order_id']);
    }

    /*
    * Relation has one with Consignment Inbound Order
    **/
    public function getConsignmentInboundOrder()
    {
        return $this->hasOne(ConsignmentInboundOrders::className(), ['id' => 'consignment_inbound_id']);
    }

    /*
   * Relation has one with Consignment Inbound Order
   **/
    public function getConsignmentOutboundOrder()
    {
        return $this->hasOne(ConsignmentOutboundOrder::className(), ['id' => 'consignment_outbound_id']);
    }

    /*
    * Relation has one with Client
    **/
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
     * Find not allocated product
     * @param integer $clientId Client id
     * @param string $productBarcode Client id
     * @return boolean
     * */
    public static function isFindFreeProduct($clientId,$productBarcode)
    {
        return Stock::find()->andWhere([
                'client_id' => $clientId,
                'product_barcode' =>$productBarcode,
                'status_availability'=>self::STATUS_AVAILABILITY_YES
            ])->exists();
    }

    public function isProductTypeReturn() {
        return $this->is_product_type = self::IS_PRODUCT_TYPE_RETURN;
    }

    public function isProductTypeLotBox() {
        return $this->is_product_type = self::IS_PRODUCT_TYPE_LOT_BOX;
    }

    public function isProductTypeLot() {
        return $this->is_product_type = self::IS_PRODUCT_TYPE_LOT;
    }


//    public static function findAndUpdate($attributes,$condition,$params)
//    {
//        if(!($s = Stock::find()->where($condition,$params)->one())) {
//            $s = new Stock();
//        }
//        $s->setAttributes($attributes);
//        $s->save(false);
//
//        return $s;
//    }


    /////////////////////

    public static function getExistInBox() {

        return [
                self::STATUS_INBOUND_SCANNING,
                self::STATUS_INBOUND_SCANNED,
                self::STATUS_INBOUND_OVER_SCANNED,
                self::STATUS_OUTBOUND_NEW,
                self::STATUS_OUTBOUND_FULL_RESERVED,
                self::STATUS_OUTBOUND_RESERVING,
                self::STATUS_OUTBOUND_PART_RESERVED,
                self::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
        ];
    }

    ///----------------------------------------------
	/*
  * Allocate outbound order
  * @param integer $outbound_order_id
  * */
	public static function allocateAnyBarcodeByOutboundOrderIdErenRetail($outbound_order_id,$address = [])
	{
//        $expected_qty = 0; // test
		if($oo = OutboundOrder::findOne($outbound_order_id)) {
			$allocatedQty = 0;
			$inboundItemsGroupBySkuIDs = [];
			$expectedQty = 0;
			if($items = $oo->getOrderItems()->all()) {

				foreach($items as $itemLine) {
					$inboundItemsGroupBySkuIDs[$itemLine->product_barcode][] = $itemLine;
				}

//                echo $oo->order_number.' '.$oo->parent_order_number;
//                VarDumper::dump($inboundItemsGroupBySkuIDs,10,true); // 223137188
//                die;

				foreach($inboundItemsGroupBySkuIDs as $skuID=>$inboundItems) {
					$currentSkuIDToLines = [];
					foreach ($inboundItems as $i=>$item) {
						if($i == 0) {
							$expectedQty += $item->expected_qty;
							$currentSkuIDToLines['exp'] = $item->expected_qty;
							$currentSkuIDToLines['res'] = 0;
						}
						$currentSkuIDToLines['lines'][$item->id] = 0;

//                        echo $oo->order_number.' '.$oo->parent_order_number;
//                        VarDumper::dump($currentExpectedSkuQty,10,true); // 223137188
//                        die;
						$item->expected_qty -= $currentSkuIDToLines['res'];
						$item->allocated_qty = 0;
						$item->status = Stock::STATUS_OUTBOUND_RESERVING;

//                        $inStocksQuery = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBox($item->product_barcode,$item->expected_qty,$oo->client_id)]); // OLD
//$inStocksQuery = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBox($skuID,$item->expected_qty,$oo->client_id)]);
						//$inStocksQuery = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBoxByProduct($item->product_barcode,$item->expected_qty,$oo->client_id)]);
						//$inStocksQuery = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBoxByProductErenRetail($item->product_barcode,$item->expected_qty,$oo->client_id)]);

						$inStocksQuery = Stock::find()
											  ->andWhere([
//											  	'id' => AllocateManager::strategyClearEmptyBoxByProductErenRetail($item->product_barcode,$item->expected_qty,$oo->client_id),
//												  'inbound_order_id'=>[116034],
												  //'product_barcode'=>$item->product_barcode,
												  'product_sku'=>$item->product_sku,
												  'client_id'=>$oo->client_id,
												  'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
												  'condition_type'=>[Stock::CONDITION_TYPE_NOT_SET,Stock::CONDITION_TYPE_UNDAMAGED],
												 // 'primary_address'=>"500000123228",
												 // 'inbound_order_id'=>122748,
											  ]);



						$inStocks = $inStocksQuery
						->orderBy('address_sort_order')
						->limit($item->expected_qty)
						->all();
						if ($inStocks) {
							foreach($inStocks as $stockLine) {
								// ORDER ITEM
								$item->allocated_qty += 1;
								$currentSkuIDToLines['res'] += 1;
								$currentSkuIDToLines['lines'][$item->id] += 1;
								$allocatedQty++;
								// STOCK
								$stockLine->outbound_order_id = $oo->id;
								$stockLine->outbound_order_item_id = $item->id;

								$stockLine->status = self::STATUS_OUTBOUND_FULL_RESERVED;
								$stockLine->status_availability = self::STATUS_AVAILABILITY_RESERVED;
								$stockLine->save(false);

							}
						}

						$item->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

						if( $item->allocated_qty == $item->expected_qty ) {
							$item->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
						}
						$item->save(false);
					}

					$linesCount = count($currentSkuIDToLines['lines']);
					$linesToDeleted = 0;
					foreach($currentSkuIDToLines['lines'] as $lineID=>$reservedByLine) {
						if(empty($reservedByLine) && $linesCount != 1 && $linesToDeleted != ($linesCount-1)) {
							$linesToDeleted += 1;
							OutboundOrderItem::deleteAll(['id'=>$lineID]);
						}
					}
				}

				$oo->expected_qty = $expectedQty;
				$oo->allocated_qty = $allocatedQty;
				$oo->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

				if( $oo->allocated_qty == $oo->expected_qty ) {
					$oo->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
				}
//
				$oo->save(false);

				// Стоит ли проверять статус для все партии
				if( $consignmentModel = ConsignmentOutboundOrder::findOne(['client_id'=>$oo->client_id,'party_number'=>$oo->parent_order_number]) ){
					$consignmentModel->allocated_qty += $allocatedQty;

					if($consignmentModel->status != Stock::STATUS_OUTBOUND_PART_RESERVED && ($oo->status == Stock::STATUS_OUTBOUND_PART_RESERVED) ) {
						$consignmentModel->status = Stock::STATUS_OUTBOUND_PART_RESERVED;
					} else {
						$consignmentModel->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
					}
					$consignmentModel->save(false);
				}

			}

//            echo $oo->order_number.' '.$oo->parent_order_number;
//            VarDumper::dump($expectedQty,10,true); // 223137188
//                die;

//            VarDumper::dump($inboundItemsGroupBySkuID,10,true); // 223137188
//            die('YYYYYYYYYYYYYYYYYY');
			return true;

			if($items = $oo->getOrderItems()->all()) {
//                VarDumper::dump($items,10,true); // 223137188
//                die('YYYYYYYYYYYYYYYYYY');
				foreach($items as $itemLine) {

					$itemLine->allocated_qty = 0;
					$itemLine->status = Stock::STATUS_OUTBOUND_RESERVING;

//                    $inStocks = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBox($itemLine->product_barcode,$itemLine->expected_qty,$oo->client_id)])
//                                ->orderBy('address_sort_order')
//                                ->all();

					$inStocksQuery = Stock::find()->andWhere(['id' => AllocateManager::strategyClearEmptyBox($itemLine->product_barcode,$itemLine->expected_qty,$oo->client_id)]);
					$inStocksCountQuery = clone $inStocksQuery;
					$inStocksCount = $inStocksCountQuery->count();
					if($inStocksCount == $itemLine->allocated_qty) {

						$inStocks = $inStocksQuery->orderBy('address_sort_order')->all();
						if ($inStocks) {
							foreach($inStocks as $stockLine) {
								// ORDER ITEM
								$itemLine->allocated_qty +=1;
								$allocate_qty++;
								//STOCK
								$stockLine->outbound_order_id = $oo->id;
								$stockLine->outbound_order_item_id = $itemLine->id;

								$stockLine->status = self::STATUS_OUTBOUND_FULL_RESERVED;
								$stockLine->status_availability = self::STATUS_AVAILABILITY_RESERVED;
								$stockLine->save(false);
								$skuIDs[$itemLine->field_extra1] +=1;
							}
						}

						$itemLine->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

						if( $itemLine->allocated_qty == $itemLine->expected_qty ) {
							$itemLine->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
						}
						$itemLine->save(false);

					} else {
//                        $itemLine->delete();

					}


				}
			}

			$oo->allocated_qty = $allocate_qty;
			$oo->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

			if( $oo->allocated_qty == $oo->expected_qty ) {
				$oo->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
			}

//            $oo->save(false);

			// Стоит ли проверять статус для все партии
			if( $consignmentModel = ConsignmentOutboundOrder::findOne(['client_id'=>$oo->client_id,'party_number'=>$oo->parent_order_number]) ){
				$consignmentModel->allocated_qty += $allocate_qty;

				if($consignmentModel->status != Stock::STATUS_OUTBOUND_PART_RESERVED && ($oo->status == Stock::STATUS_OUTBOUND_PART_RESERVED) ) {
					$consignmentModel->status = Stock::STATUS_OUTBOUND_PART_RESERVED;
				} else {
					$consignmentModel->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
				}

//                $consignmentModel->save(false);
			}
		}
	}

}
