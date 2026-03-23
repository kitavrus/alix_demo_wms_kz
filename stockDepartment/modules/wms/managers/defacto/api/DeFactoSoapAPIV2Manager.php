<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 27.03.15
 * Time: 08:53
 */

namespace stockDepartment\modules\wms\managers\defacto\api;

use common\components\DeliveryProposalManager;
use common\helpers\DateHelper;
use common\modules\client\models\Client;
use common\modules\codebook\models\Codebook;
use common\modules\crossDock\models\ConsignmentCrossDock;
use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\product\models\defacto\Products;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItemProduct;
use common\modules\stock\models\ConsignmentUniversal;
use common\modules\stock\models\ConsignmentUniversalOrders;
use common\modules\stock\models\ConsignmentUniversalOrdersItems;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use yii\data\ActiveDataProvider;
//use yii\helpers\ArrayHelper;
use common\overloads\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\db\Expression;
use yii\helpers\BaseFileHelper;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\components\BarcodeManager;
use common\components\MailManager;
use common\modules\product\service\ProductService;


class DeFactoSoapAPIV2Manager
{

    const INBOUND_STATUS_NOTHING = 1; // новый
    const INBOUND_STATUS_MARKED_FOR_INBOUND_DATA = 2; // В процессе подготовки. Ждем статус: InBoundDataIsPrepared
    const INBOUND_STATUS_DATA_IS_PREPARED = 3; // 'Можно использовать метод GetAppointmentInBoundData
    const INBOUND_STATUS_COMPLETED = 4; // 'Заказ выполнен

    const OUTBOUND_STATUS_MARKED_FOR_OUTBOUND_DATA = 5; // В процессе подготовки. Ждем статус: InBoundDataIsPrepared


    /*
    * @var array Default format function result
    * */
    public $_outResult = [
        'HasError' => true,
        'ErrorMessage' => '',
        'Message' => '',
        'Data' => [],
    ];
    /*
     *
     * */
    private $_consignmentUniversalModel;
    private $_consignmentUniversalId = 0;
	
	    private $skuIDsWithProducts = [];

    /*
     * */
    public function __construct($consignmentUniversalId = null)
    {
        $this->_consignmentUniversalId = $consignmentUniversalId;
        $this->skuIDsWithProducts['226794987'] = ['2300018179056'];
        $this->skuIDsWithProducts['226730529'] = ['2300017863543'];
        $this->skuIDsWithProducts['226487318'] = ['2300016715515'];
        $this->skuIDsWithProducts['226828153'] = ['2300018353395'];
    }

    /*
     *
     * */
    public function getConsignmentUniversalId()
    {
        return $this->_consignmentUniversalId;
    }

    /*
     *
     * */
    public function setConsignmentUniversalId($consignmentUniversalId = null)
    {
        return $this->_consignmentUniversalId = $consignmentUniversalId;
    }

/////////////////////// GET WAREHOUSE APPOINTMENTS /////////////////////////////////////////////////////////////
    /*
     * Получаем список приходных накладных по апи с созданяем в нащшу базу
     *
     * */
    public function getAndSaveInboundOrderParty()
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->GetWarehouseAppointments();

        if (!$dataFromAPI['HasError']) {
            $preparedData = $this->preparedInboundOrderPartyForSaveToDb($dataFromAPI['Data']);
            if (!$preparedData['HasError']) {
                $saveToDbData = $this->saveInboundOrderPartyToDb($preparedData['Data']);
                if (!$saveToDbData['HasError']) {
                    $outResult['HasError'] = false;
                    $outResult['Message'] = $saveToDbData['Message'];
                    return $outResult;
                } else {
                    $outResult['ErrorMessage'] = $saveToDbData['ErrorMessage'];
                }
            } else {
                $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
            }
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }

        return $outResult;
    }

    /* Подготоваливаем данные полученые по апи от клиента с помощью GetWarehouseAppointments для сохранения в нашу базу
       * @param array stdClass $data. Example values:
       * [Id] => 4
       * [WarehouseId] => 1
       * [AppointmentUniversalIdentifierId] => 2743613
       * [AppointmentDate] => '2016-04-11T19:00:00'
       * [Active] => true
       * [BusinessUnitId] => 1034
       * [AppointmentBarcode] => 'D10AA00000128'
       * [WarehouseAppointmentWMSStatus] => 'MarkedforInBoundData'
       * @return array
       * */
    private function preparedInboundOrderPartyForSaveToDb($data)
    {
        $outResult = $this->_outResult;
        $result = [];
        if (empty($data)) {
            $outResult['ErrorMessage'] = 'Нет данных для подготовки';
            return $outResult;
        }
        foreach ($data as $value) {
            $Id = ArrayHelper::getValue($value, 'Id', '');
            $BusinessUnitId = ArrayHelper::getValue($value, 'BusinessUnitId', '');
            $ForeignInvoiceNumber = ArrayHelper::getValue($value, 'ForeignInvoiceNumber', '');
            $AppointmentBarcode = ArrayHelper::getValue($value, 'AppointmentBarcode', '');
            $AppointmentDate = ArrayHelper::getValue($value, 'AppointmentDate', '');
            $Explanation = ArrayHelper::getValue($value, 'Explanation', '');
            $Status = ArrayHelper::getValue($value, 'Status', '');

            $apiLogValue = [
                'Id' => $Id,
                'BusinessUnitId' => $BusinessUnitId,
                'ForeignInvoiceNumber' => $ForeignInvoiceNumber,
                'AppointmentBarcode' => $AppointmentBarcode,
                'AppointmentDate' => $AppointmentDate,
                'Explanation' => $Explanation,
                'Status' => $Status,
            ];

            $result[] = [
                'client_id' => Client::CLIENT_DEFACTO,
                'party_number' => $ForeignInvoiceNumber,
                'status_created_on_client' => $Status,
                'data_created_on_client' => DateHelper::formatDefactoDate($AppointmentDate),
                'order_type' => ConsignmentUniversal::ORDER_TYPE_INBOUND,
                'field_extra1' => $AppointmentBarcode,
                'extra_fields' => Json::encode(['apiLogValue' => $apiLogValue]),
            ];
        }

        $outResult['HasError'] = false;
        $outResult['Data'] = $result;

        return $outResult;
    }

    /*
     * @param array $data
     * */
    private function saveInboundOrderPartyToDb($data)
    {
        $outResult = $this->_outResult;
        if (empty($data)) {
            $outResult['ErrorMessage'] = 'Нет данных для сохранения в базу';
            return $outResult;
        }

        foreach ($data as $value) {

            $cu = ConsignmentUniversal::findOne([
                'field_extra1' => $value['field_extra1'],
                'client_id' => $value['client_id'],
                'order_type' => ConsignmentUniversal::ORDER_TYPE_INBOUND,
            ]);

            if (!$cu) {
                $cu = new ConsignmentUniversal();
                $value['status'] = ConsignmentUniversal::STATUS_INBOUND_NEW;
            }
            $cu->setAttributes($value, false);
            $cu->save(false);
        }

        $outResult['HasError'] = false;
        $outResult['Message'] = 'Данные в базу успешно сохранены';

        return $outResult;
    }

/////////////////////// MARK APPOINTMENT FOR INBOUND /////////////////////////////////////////////////////////////
    /*
    * Уведомляем по средством апи сторону девакта что заданная приходная накладная прибыла к нам на склад. Т.е грузовик приехал к нам на склад но мы его еще не начали разгружать.
    * @param string $id
    * */
    public function saveMarkInboundPartyById()
    {
        $outResult = $this->_outResult;

        $id = $this->getConsignmentUniversalId();

        if ($cu = ConsignmentUniversal::findOne(['id' => $id, 'status_created_on_client' => DeFactoSoapAPIV2::INBOUND_STATUS_NOTHING])) {
            $dataFromAPI = $this->sendMarkInboundParty($cu->field_extra1);
            if (!$dataFromAPI['HasError']) {
                $cu->status = ConsignmentUniversal::STATUS_INBOUND_LOADED_FROM_API;
                $cu->save(false);

                $outResult['HasError'] = false;
                $outResult['Message'] = 'Данные подготавливаются. Пожалуйста подождите 15 мин';
            } else {
                $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
            }

        } else {
            $outResult['ErrorMessage'] = 'Накладная с id ' . $id . '  и статусом NOTHING не найдена';
        }
        return $outResult;
    }

    /*
    * Уведомляем по средством апи сторону дефакта что заданная приходная накладная прибыла к нам на склад. Т.е грузовик приехал к нам на склад но мы его еще не начали принимать.
    * @param string $appointmentBarcode
    * */
    private function sendMarkInboundParty($appointmentBarcode)
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->MarkAppointmentforInBound($appointmentBarcode);
        if (!$dataFromAPI['HasError']) {
            $outResult['HasError'] = false;
            $outResult['Message'] = 'Данные успешно переданы дефакто';
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }
        return $outResult;
    }
/////////////////////// GET APPOINTMENT INBOUND DATA /////////////////////////////////////////////////////////////
    /*
    * Уведомляем по средством апи сторону девакта что заданная приходная накладная прибыла к нам на склад. Т.е грузовик приехал к нам на склад но мы его еще не начали разгружать.
    * @param string $appointmentBarcode
    * */
    public function getAndSaveInboundOrderPartyItems()
    {
        $outResult = $this->_outResult;
        $id = $this->getConsignmentUniversalId();
        $cu = ConsignmentUniversal::findOne([
            'id' => $id,
            'status_created_on_client' => DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED,
            'status' => [ConsignmentUniversal::STATUS_INBOUND_LOADED_FROM_API, ConsignmentUniversal::STATUS_INBOUND_NEW]
        ]);

        if (!$cu) {
            $outResult['ErrorMessage'] = 'Эта накладная уже загружена или ее несуществует';
            return $outResult;
        }

        $inInbound = InboundOrder::find()->andWhere(['order_number'=>$cu->field_extra1])->exists();
        $inCrossDock = CrossDock::find()->andWhere(['party_number'=>$cu->field_extra1])->exists();
        if($inInbound || $inCrossDock) {
            $outResult['ErrorMessage'] = 'Эта накладная уже загружена';
            return $outResult;
        }

        $appointmentBarcode = $cu->field_extra1;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->GetAppointmentInBoundData($appointmentBarcode);

        if ($dataFromAPI['HasError']) {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
            return $outResult;
        }

        $preparedData = $this->preparedInboundOrderPartyItemForSaveToDb($dataFromAPI['Data']);
        if ($preparedData['HasError']) {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
            return $outResult;
        }

        $saveToDbData = $this->saveInboundOrderPartyItemsToDb($preparedData['Data']);
        if ($saveToDbData['HasError']) {
            $outResult['ErrorMessage'] = $saveToDbData['ErrorMessage'];
            return $outResult;
        }

        $outResult['HasError'] = false;
        $outResult['Message'] = $saveToDbData['Message'];
        return $outResult;
    }

    /* Подготоваливаем данные полученые по апи от клиента с помощью GetWarehouseAppointments для сохранения в нашу базу
      * @param array stdClass $data. Example values:
      * [Id] => 139
      * [FromBusinessUnitId] => 1026
      * [PackBarcode] => '2430000038169'
      * [PackQuantity] => '1.00'
      * [SkuId] => 2166809
      * [SkuBarcode] => '9000004818978'
      * [SkuQuantity] => '2.00'
      * [Status] => 'ReadyforProcessing'
      * [AppointmentBarcode] => 'D10AA00000043'
      * [ToBusinessUnitId] => 1029
      * [FlowType] => 'ECommercePreAdmission'
      * @return array
      * */
    private function preparedInboundOrderPartyItemForSaveToDb($data)
    {
        $outResult = $this->_outResult;
        $result = [];
        if (!empty($data)) {
            foreach ($data as $value) {

                $id = ArrayHelper::getValue($value, 'Id', '');
                $FromBusinessUnitId = ArrayHelper::getValue($value, 'FromBusinessUnitId', '');
                $PackBarcode = ArrayHelper::getValue($value, 'LcOrCartonLabel', ''); // PackBarcode
                $PackQuantity = ArrayHelper::getValue($value, 'NumberOfCartons', ''); // PackQuantity
                $SkuId = ArrayHelper::getValue($value, 'SkuId', '');
                $SkuBarcode = ArrayHelper::getValue($value, 'LotOrSingleBarcode', ''); // SkuBarcode
                $SkuQuantity = ArrayHelper::getValue($value, 'LotOrSingleQuantity', ''); // SkuQuantity
                $Status = ArrayHelper::getValue($value, 'Status', '');
                $AppointmentBarcode = ArrayHelper::getValue($value, 'AppointmentBarcode', '');
                $ToBusinessUnitId = ArrayHelper::getValue($value, 'ToBusinessUnitId', '');
                $FlowType = ArrayHelper::getValue($value, 'FlowType', '');

                $apiLogValue = [
                    'id' => $id,
                    'FromBusinessUnitId' => $FromBusinessUnitId,
                    'LcOrCartonLabel' => $PackBarcode, // PackBarcode
                    'NumberOfCartons' => $PackQuantity, // PackQuantity
                    'SkuId' => $SkuId,
                    'LotOrSingleBarcode' => $SkuBarcode, // SkuBarcode
                    'LotOrSingleQuantity' => $SkuQuantity, // SkuQuantity
                    'Status' => $Status,
                    'AppointmentBarcode' => $AppointmentBarcode,
                    'ToBusinessUnitId' => $ToBusinessUnitId,
                    'FlowType' => $FlowType,
                ];

                $result[] = [
                    'client_id' => Client::CLIENT_DEFACTO,
                    'from_point_client_id' => $FromBusinessUnitId,
                    'to_point_client_id' => $ToBusinessUnitId,
                    'box_barcode_client' => $PackBarcode,
                    'product_barcode' => $SkuBarcode,
                    'expected_number_places_qty' => $PackQuantity,
                    'expected_qty' => $SkuQuantity,
                    'field_extra1' => $SkuId,
                    'field_extra2' => $id,
                    'order_type' => 1,
                    'order_type_client' => $FlowType,
                    'status_created_on_client' => $Status,
                    'extra_fields' => Json::encode(['apiLogValue' => $apiLogValue]),
                ];
            }

            $outResult['HasError'] = false;
            $outResult['Data'] = $result;

        } else {
            $outResult['ErrorMessage'] = 'Нет данных для подготовки';
        }

        return $outResult;
    }

    /*
    * Получаем данне по апи и сохраняем их в таблицы с приходными накладными
    * @param array $data
    * */
    public function saveInboundOrderPartyItemsToDb($data) // OK
    {
        $outResult = $this->_outResult;
        $id = $this->getConsignmentUniversalId();
        $boxQtyArray = [];
        if (!empty($data)) {
            $cu = ConsignmentUniversal::findOne([
                'id' => $id,
                'status_created_on_client' => DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED,
                'status' => [ConsignmentUniversal::STATUS_INBOUND_LOADED_FROM_API, ConsignmentUniversal::STATUS_INBOUND_NEW],
            ]);
            if ($cu) {
                if (!($cuOrder = ConsignmentUniversalOrders::findOne(['consignment_universal_id' => $cu->id, 'client_id' => $cu->client_id]))) {
                    $cuOrder = new ConsignmentUniversalOrders();
                }
                $cuOrder->consignment_universal_id = $cu->id;
                $cuOrder->client_id = $cu->client_id;
                $cuOrder->status = Stock::STATUS_INBOUND_NEW;
                $cuOrder->order_number = $cu->field_extra1;
                $cuOrder->party_number = $cu->party_number;
                $cuOrder->order_type = InboundOrder::ORDER_TYPE_INBOUND;
                $cuOrder->expected_qty = 0;
                $cuOrder->accepted_qty = 0;
                $cuOrder->save(false);
                ConsignmentUniversalOrdersItems::deleteAll($cuOrder->id);
                foreach ($data as $value) {

                    $value['consignment_universal_id'] = $id;
                    $value['consignment_universal_order_id'] = $cuOrder->id;

//                    if (!($inItem = ConsignmentUniversalOrdersItems::findOne([
//                            'consignment_universal_id' => $id,
//                            'consignment_universal_order_id' => $cuOrder->id,
//                            'from_point_client_id' => $value['from_point_client_id'],
//                            'to_point_client_id' => $value['to_point_client_id'],
//                            'box_barcode_client' => $value['box_barcode_client'],
//                            'expected_number_places_qty' => $value['expected_number_places_qty'],
//                            'product_barcode' => $value['product_barcode'],
//                            'field_extra1' => $value['field_extra1'],
//                            'field_extra2' => $value['field_extra2'],
//                        ]
//                    ))
//                    ) {
//                        $inItem = new ConsignmentUniversalOrdersItems();
//                    }
                    $inItem = new ConsignmentUniversalOrdersItems();
                    $inItem->setAttributes($value, false);
                    $inItem->save(false);

                    $boxQtyArray[$value['box_barcode_client']] = $value['expected_number_places_qty'];
                }

                $boxQty = array_sum($boxQtyArray);

                $cuOrder->expected_number_places_qty = $boxQty;
                $cuOrder->save(false);

                $cu->status = ConsignmentUniversal::STATUS_INBOUND_LOADED_SAVED;
                $cu->expected_number_places_qty = $boxQty;
                $cu->save(false);

//                $outResult['Data']['boxQty'] = $boxQty;
                $outResult['HasError'] = false;
                $outResult['Message'] = 'Данные в базу успешно сохранены';
            } else {
                $outResult['ErrorMessage'] = 'Накладная с id ' . $id . '  и статусом ' . DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED . ' не найдена. возможно данные еще не подготовлены. Попробуйте снова через 5 мин';
            }
        } else {
            $outResult['ErrorMessage'] = 'Нет данных для сохранения в базу-1';
        }
        return $outResult;
    }

    /*
     * Сохраняем данные о приходе. Те товары которые нужно принят на склад
     * */
    public function saveInboundInStockToDb() // OK
    {
        $outResult = $this->_outResult;
        $id = $this->getConsignmentUniversalId();
        $cu = ConsignmentUniversal::findOne([
            'id' => $id,
            'status_created_on_client' => DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED,
            'status' => [ConsignmentUniversal::STATUS_INBOUND_LOADED_SAVED]
//            'status' => [ConsignmentUniversal::STATUS_INBOUND_LOADED_FROM_API, ConsignmentUniversal::STATUS_INBOUND_NEW]
        ]);
        if ($cu) {
            $cuOrder = ConsignmentUniversalOrders::findOne(['consignment_universal_id' => $cu->id, 'client_id' => $cu->client_id]);
            $cuoItems = ConsignmentUniversalOrdersItems::findAll(['consignment_universal_id' => $cu->id, 'consignment_universal_order_id' => $cuOrder->id]);

            $inboundDataToSave = [];
            $crossDockDataToSave = [];
            $boxQty = [];
            $inboundBoxQty = [];
            if (!empty($cuoItems)) {
                foreach ($cuoItems as $item) {
                    $boxQty[$item['box_barcode_client']] = $item['box_barcode_client'];
                    if (($item['order_type_client'] == 'ECommercePreAdmission' || $item['order_type_client'] == 'PutAway')) {
                        $inboundDataToSave [] = [
                            'product_barcode' => $item['product_barcode'],
                            'expected_qty' => $item['expected_qty'],
                            'status' => Stock::STATUS_INBOUND_NEW,
                            'product_serialize_data' => Json::encode($item),
                            'consignment_universal_orders_item_id' => $item['id'],
                            'box_barcode' => $item['box_barcode_client'],
                            'expected_number_places_qty' => $item['expected_number_places_qty'],
                        ];
                        $inboundBoxQty[$item['box_barcode_client']] = $item['box_barcode_client'];
                    }

                    if ($item['order_type_client'] == 'CrossDock' || $item['order_type_client'] == 'SortCrossDock') {
                        $crossDockDataToSave[$item['to_point_client_id']][] = [
                            'box_barcode' => $item['box_barcode_client'],
                            'expected_number_places_qty' => $item['expected_number_places_qty'],
                            'box_m3' => '0',
                            'weight_net' => '0',
                            'weight_brut' => '0',
                            'product_serialize_data' => Json::encode($item),
                            'field_extra1' => $item['field_extra2'],
                            'field_extra2' => $item['product_barcode'],
                        ];
                    }
                }
            }
            // INBOUND
            if (!empty($inboundDataToSave)) {
                if (!($in = InboundOrder::findOne([
                    'parent_order_number' => $cuOrder->party_number,
                    'order_number' => $cuOrder->order_number,
                    'client_id' => $cuOrder->client_id]))
                ) {
                    $in = new InboundOrder();
                }

                $in->client_id = $cu->client_id;
                $in->status = Stock::STATUS_INBOUND_NEW;
                $in->order_number = $cuOrder->order_number;
                $in->parent_order_number = $cuOrder->party_number;
                $in->order_type = InboundOrder::ORDER_TYPE_INBOUND;
                $in->expected_qty = 0;
                $in->accepted_qty = 0;
                $in->save(false);

                // Добавляем линии с товарами
                $expectedQty = 0;
                InboundOrderItem::deleteAll(['inbound_order_id' => $in->id]);
                Stock::deleteAll(['inbound_order_id' => $in->id]);
                foreach ($inboundDataToSave as $item) {
                    $attribute = [
                        'inbound_order_id' => $in->id,
                        'product_barcode' => $item['product_barcode'],
                        'expected_qty' => $item['expected_qty'],
                        'status' => $item['status'],
                        'product_serialize_data' => $item['product_serialize_data'],
                        'box_barcode' => $item['box_barcode'],
                        'expected_number_places_qty' => $item['expected_number_places_qty'],

                    ];
                    $inItem = new InboundOrderItem();
                    $inItem->setAttributes($attribute, false);
                    $inItem->save(false);

                    $expectedQty += $item['expected_qty'];
                    for ($i = 1; $i <= $item['expected_qty']; ++$i) {
                        $stock = new Stock();
                        $stock->client_id = $in->client_id;
                        $stock->inbound_order_id = $in->id;
                        $stock->inbound_order_item_id = $inItem->id;
                        $stock->product_barcode = $item['product_barcode'];
                        $stock->product_model = '';
                        $stock->status = Stock::STATUS_INBOUND_NEW;
                        $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
                        $stock->inbound_client_box = $item['box_barcode'];
                        $stock->save(false);
                    }
                }
                $in->expected_qty = $expectedQty;
                $in->save(false);
            }
            // CROSS-DOCK
            if (!empty($crossDockDataToSave)) {
                $partyNumber = $cu->field_extra1;
                $cCD = ConsignmentCrossDock::findOne(['client_id' => $cuOrder->client_id, 'party_number' => $partyNumber]);
                if (!$cCD) {
                    $cCD = new ConsignmentCrossDock();
                    $cCD->client_id = $cuOrder->client_id;
                    $cCD->party_number = $partyNumber;
                    $cCD->expected_rpt_places_qty = count($inboundBoxQty); // ?? TODO добавить количество коробов которые нужно принят на склад
                    $cCD->expected_number_places_qty = 0;
                }
                $cCD->expected_number_places_qty = 0;
                $cCD->save(false);

                // Добавляем линии с товарами
                foreach ($crossDockDataToSave as $toPointClientId => $items) {

                    if (!($cd = CrossDock::findOne([
                        'client_id' => $cuOrder->client_id,
                        'party_number' => $partyNumber,
                        'to_point_title' => $toPointClientId,
                    ]))
                    ) {
                        $cd = new CrossDock();
                        $cd->consignment_cross_dock_id = $cCD->id;
                        $cd->client_id = $cuOrder->client_id;
                        $cd->internal_barcode = $cuOrder->client_id . '-' . $cuOrder->party_number;
                        $cd->order_number = $cu->field_extra1 . '-' . $toPointClientId;
                        $cd->party_number = $cu->field_extra1; //$cuOrder->party_number;
                        $cd->to_point_title = $toPointClientId;
                        $cd->to_point_id = $this->getStore($toPointClientId); //TODO
                        $cd->from_point_id = '4'; // НАШ склад ;
                        $cd->from_point_title = '4';
                        $cd->weight_net = 0;
                        $cd->weight_brut = 0;
                        $cd->box_m3 = 0;
                        $cd->expected_number_places_qty = 0;
                    }
                    $cd->status = Stock::STATUS_CROSS_DOCK_NEW;
                    $cd->save(false);
                    $boxM3 = 0.096;

                    CrossDockItems::deleteAll(['cross_dock_id' => $cd->id]);
                    $boxBarcodeCount = [];
                    foreach ($items as $item) {
                        $crossDockItem = new CrossDockItems();
                        $crossDockItem->cross_dock_id = $cd->id;
                        $crossDockItem->box_barcode = $item['box_barcode'];
                        $crossDockItem->expected_number_places_qty = $item['expected_number_places_qty'];
                        $crossDockItem->box_m3 = $boxM3;
                        $crossDockItem->weight_net = $item['weight_net'];
                        $crossDockItem->weight_brut = $item['weight_brut'];
                        $crossDockItem->product_serialize_data = $item['product_serialize_data'];
                        $crossDockItem->field_extra1 = $item['field_extra1'];
                        $crossDockItem->field_extra2 = $item['field_extra2'];
                        $crossDockItem->field_extra3 = $cuOrder->party_number;
                        $crossDockItem->save(false);
                        $boxBarcodeCount[$item['box_barcode']] = $item['box_barcode'];
//                        $expectedQty += $crossDockItem->expected_number_places_qty;
                    }

                    $cd->expected_number_places_qty = count($boxBarcodeCount);
                    $cd->save(false);
                    $cCD->expected_number_places_qty = $cd->expected_number_places_qty;
                    $cCD->save(false);
                }
            }
            $cu->status = ConsignmentUniversal::STATUS_INBOUND_COMPLETE;
            $cu->save(false);


            $outResult['HasError'] = false;
            $outResult['Message'] = 'Данные в базу успешно сохранены';
        } else {
            $outResult['ErrorMessage'] = 'Нет данных для сохранения в базу';
        }
        return $outResult;
    }

    /*
     *
     * */
    public function SendInBoundFeedBackData($data) // OK
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->SendInBoundFeedBackData($data);
        if (!$dataFromAPI['HasError']) {
            $outResult['HasError'] = false;
            $outResult['Message'] = 'Данные успешно переданы дефакто';
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }
        return $outResult;
    }

    /*
     *
     * */
    public function SendOutBoundFeedBackData($data) //
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->SendOutBoundFeedBackData($data);
        if (!$dataFromAPI['HasError']) {
            $outResult['HasError'] = false;
            $outResult['Message'] = 'Данные успешно переданы дефакто';
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }
        return $outResult;
    }

    /*
     *
     * */
    public static function preparedSendInBoundFeedBackData($item, $AppointmentBarcode = null) // OK
    {
        $jsonProductData = [];
        if (!empty($item->product_serialize_data)) {
            $jsonProductData = Json::decode($item->product_serialize_data);
            $jsonProductDataLet = [];
            if ($extraFieldLet = ArrayHelper::getValue($jsonProductData, 'extra_fields')) {
                $jsonProductDataLet = Json::decode($extraFieldLet);
            }
            $jsonProductData['extra_fields'] = $jsonProductDataLet;
        }

        $InBoundId = ArrayHelper::getValue($jsonProductData, 'field_extra2');
        if (is_null($AppointmentBarcode)) {
            $AppointmentBarcode = ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.AppointmentBarcode');
        }
        $LcOrCartonLabel = ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.LcOrCartonLabel');
        $LotOrSingleBarcode = $item->product_barcode;
        $LotOrSingleQuantity = $item->accepted_qty;

        $rows = [];
        // количество принятых лотов меньше или равно ожидаемому
        if ($item->accepted_qty <= $item->expected_qty && $item->accepted_qty > 0) {
            $rows[] = [
                'InboundId' => $InBoundId,//'48', // если плюсы то передаем null
                'AppointmentBarcode' => $AppointmentBarcode,//'D10AA00000043',
                'LcOrCartonLabel' => $LcOrCartonLabel, //'2430000072423',
                'LotOrSingleBarcode' => $LotOrSingleBarcode, //'9000003635927',
                'LotOrSingleQuantity' => $LotOrSingleQuantity, //'1',
				'IsDamaged' => false
            ];
        }
        // Если в коробе который мы принимаем больше лотов чем мы ожидаем
        if ($item->accepted_qty > $item->expected_qty && $item->accepted_qty > 0 && $item->expected_qty != 0) {
            $plusQty = $item->accepted_qty - $item->expected_qty;

            $LotOrSingleQuantity = $item->expected_qty;

            $rows[] = [
                'InboundId' => $InBoundId,//'48', // если плюсы то передаем null
                'AppointmentBarcode' => $AppointmentBarcode,//'D10AA00000043',
                'LcOrCartonLabel' => $LcOrCartonLabel, //'2430000072423',
                'LotOrSingleBarcode' => $LotOrSingleBarcode, //'9000003635927',
                'LotOrSingleQuantity' => $LotOrSingleQuantity, //'1',
				'IsDamaged' => false
            ];

            $rows[] = [
                'InboundId' => null,//'48', // если плюсы то передаем null
                'AppointmentBarcode' => $AppointmentBarcode,//'D10AA00000043',
                'LcOrCartonLabel' => $LcOrCartonLabel, //'2430000072423',
                'LotOrSingleBarcode' => $LotOrSingleBarcode, //'9000003635927',
                'LotOrSingleQuantity' => $plusQty, //'1',
				'IsDamaged' => false
            ];
        }

        // Если нет ни короба не лота в накладной
        if ($item->expected_qty == 0 && $item->accepted_qty > 0) {
            $LotOrSingleQuantity = $item->accepted_qty;
            $LcOrCartonLabel = $item->box_barcode;

            $rows[] = [
                'InboundId' => null,//'48', // если плюсы то передаем null
                'AppointmentBarcode' => $AppointmentBarcode,//'D10AA00000043',
                'LcOrCartonLabel' => $LcOrCartonLabel, //'2430000072423',
                'LotOrSingleBarcode' => $LotOrSingleBarcode, //'9000003635927',
                'LotOrSingleQuantity' => $LotOrSingleQuantity, //'1',
				'IsDamaged' => false
            ];
        }

        return $rows;
    }

    /*
     *
     * */
    public static function preparedSendInBoundFeedBackDataCrossDock($item) // OK
    {
        $jsonProductData = [];
        if (!empty($item->product_serialize_data)) {
            $jsonProductData = Json::decode($item->product_serialize_data);
            $jsonProductDataLet = [];
            if ($extraFieldLet = ArrayHelper::getValue($jsonProductData, 'extra_fields')) {
                $jsonProductDataLet = Json::decode($extraFieldLet);
            }
            $jsonProductData['extra_fields'] = $jsonProductDataLet;
        }

        $InBoundId = $item->field_extra1;
        $AppointmentBarcode = ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.AppointmentBarcode');
        $LotOrSingleQuantity = ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.LotOrSingleQuantity');
        $LcOrCartonLabel = $item->box_barcode;
        $LotOrSingleBarcode = $item->field_extra2;

        $rows = [];
        $rows[] = [
            'InboundId' => $InBoundId,
            'AppointmentBarcode' => $AppointmentBarcode,//'D10AA00000043',
            'LcOrCartonLabel' => $LcOrCartonLabel, //'2430000072423',
            'LotOrSingleBarcode' => $LotOrSingleBarcode, //'9000003635927',
            'LotOrSingleQuantity' => $LotOrSingleQuantity,
			'IsDamaged' => false
        ];

        return $rows;
    }

    /*
    *
    * */
    public static function preparedSendOutBoundFeedBackData($stocks, $outboundOrderItem)
    {
        $jsonProductData = [];
        if (!empty($outboundOrderItem->product_serialize_data)) {
            $jsonProductData = Json::decode($outboundOrderItem->product_serialize_data);
            $jsonProductDataLet = [];
            if ($extraFieldLet = ArrayHelper::getValue($jsonProductData, 'extra_fields')) {
                $jsonProductDataLet = Json::decode($extraFieldLet);
            }
            $jsonProductData['extra_fields'] = $jsonProductDataLet;
        }

        $row = [];
        foreach ($stocks as $stock) {
            $OutBoundId = ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.OutboundId');
            $InBoundId = '';
            $LcBarcode = $stock['box_barcode'];
            $LotOrSingleBarcode = $stock['product_barcode'];
            $Volume = !empty($stock['box_size_barcode']) ? $stock['box_size_barcode'] : 32;
            $InvoiceNumber = '';// $AppointmentBarcode = ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.AppointmentBarcode');;
            $SkuQuantity = $stock['accepted_qty'];
            $WaybillSerial = 'KZK';
            $CargoShipmentNo = '1';
//            $WaybillNumber = isset($stock['our_box_barcode']) && !empty($stock['our_box_barcode']) ? $stock['our_box_barcode'] : $stock['box_barcode'] ; //TODO Если я сюда ставлю что-то отличное от единицы получаю ошибку. //            $WaybillNumber = '1'; //TODO Если я сюда ставлю что-то отличное от единицы получаю ошибку.
            $WaybillNumber = isset($stock['waybill_number']) && !empty($stock['waybill_number']) ? $stock['waybill_number'] : $stock['box_barcode']; //TODO Если я сюда ставлю что-то отличное от единицы получаю ошибку. //            $WaybillNumber = '1'; //TODO Если я сюда ставлю что-то отличное от единицы получаю ошибку.
            if ($inboundOrder = InboundOrder::findOne($stock['inbound_order_id'])) {
                $InvoiceNumber = $inboundOrder->parent_order_number;
            }

            $row[] = [
                'OutBoundId' => $OutBoundId,//'16', // это ид из SendOutBoundFeedBackData
                'InBoundId' => $InBoundId,//'0', // Что это такое ?
                'LcBarcode' => $LcBarcode,// OLD PackBarcode '700000001', // наш короб
                'LotOrSingleBarcode' => $LotOrSingleBarcode,// OLD SkuBarcode '8680654893689', // Что это такое ?
                'LotOrSingleQuantity' => $SkuQuantity,//OLD SkuQuantity '12', // кол-во лотов в коробе
                'WaybillSerial' => $WaybillSerial,//'KZK', //это не меняется
                'WaybillNumber' => $WaybillNumber,//'16', //ReservationId - брать из GetWarehousePickings (его тут нет) и он пуст в GetPickingOutBoundData
                'Volume' => $Volume,//'32', //размер короба это mapM3ToBoxSize 12, 17, 31 и т.д.
                'CargoShipmentNo' => $CargoShipmentNo,//'-', //не используем
                'InvoiceNumber' => $InvoiceNumber,//это номер приходной накладно по которой мы приняли этот товар
                'our_box_barcode' => isset($stock['our_box_barcode']) ? $stock['our_box_barcode'] : '',
            ];
        }

        return $row;
    }

    /*
     * Получаем список приходных накладных по апи с созданяем в нащшу базу
     *
     * */
    public function getAndSaveOutboundOrderParty()
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->GetWarehousePickings();
//        VarDumper::dump($dataFromAPI,10,true);
//        die;
        if (!$dataFromAPI['HasError']) {
            $preparedData = $this->preparedOutboundOrderPartyForSaveToDb($dataFromAPI['Data']);
            if (!$preparedData['HasError']) {
                $saveToDbData = $this->saveOutboundOrderPartyToDb($preparedData['Data']);
                if (!$saveToDbData['HasError']) {
                    $outResult['HasError'] = false;
                    $outResult['Message'] = $saveToDbData['Message'];
                    return $outResult;
                } else {
                    $outResult['ErrorMessage'] = $saveToDbData['ErrorMessage'];
                }
            } else {
                $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
            }
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }

        return $outResult;
    }

    /*
    * Получаем содержимое расходной накладной
    * @param string $appointmentBarcode
    * */
    public function getAndSaveOutboundOrderPartyItems()
    {
        $outResult = $this->_outResult;
        $id = $this->getConsignmentUniversalId();
        $cu = ConsignmentUniversal::findOne([
            'id' => $id,
            'status_created_on_client' => [DeFactoSoapAPIV2::OUTBOUND_STATUS_NEW, DeFactoSoapAPIV2::OUTBOUND_STATUS_DATA_IS_PREPARED],
            'order_type' => ConsignmentUniversal::ORDER_TYPE_OUTBOUND,
        ]);
        if (!$cu) {
            $outResult['ErrorMessage'] = "Накладная не найдена";
            return $outResult;
        }

        $inOutbound = OutboundOrder::find()->andWhere(['parent_order_number'=>$cu->party_number])->exists();
        if ($inOutbound) {
            $outResult['ErrorMessage'] = "Накладная уже загружена!!!";
            return $outResult;
        }

        $pickingID = $cu->party_number;

        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->GetOutBoundData($pickingID);

        if (!$dataFromAPI['HasError']) {
            $preparedData = $this->preparedOutboundOrderPartyItemForSaveToDb($dataFromAPI['Data']);
//                file_put_contents("xxxx.txt",print_r($preparedData,true),FILE_APPEND);
//                die("ddddd");
            if (!$preparedData['HasError']) {
                $saveToDbData = $this->saveOutboundOrderPartyItemsToDb($preparedData['Data']);
                if (!$saveToDbData['HasError']) {
                    $outResult['HasError'] = false;
                    $outResult['Message'] = $saveToDbData['Message'];
                    return $outResult;
                } else {
                    $outResult['ErrorMessage'] = $saveToDbData['ErrorMessage'];
                }
            } else {
                $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
            }
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }
        return $outResult;
    }


    /*
     * @return array
     * */
    public function preparedOutboundOrderPartyItemForSaveToDb($data)
    {
        $outResult = $this->_outResult;
        $result = [];
        if (!empty($data)) {
            $data = count($data) == 1 ? [$data] : $data;
            foreach ($data as $value) {

                $SkuId = ArrayHelper::getValue($value, 'SkuId', '');
                $OutboundId = ArrayHelper::getValue($value, 'OutboundId', '');

                if($barcodeLots = $this->getProductsBySkuID($SkuId)) {

                    $BatchId = ArrayHelper::getValue($value, 'BatchId', '');
                    $ReservationId = ArrayHelper::getValue($value, 'ReservationId', '');
                    $Quantity = ArrayHelper::getValue($value, 'Quantity', '');
                    $Status = ArrayHelper::getValue($value, 'Status', '');
                    $ToBusinessUnitId = ArrayHelper::getValue($value, 'ToBusinessUnitId', '');
                    $CargoBusinessUnitId = ArrayHelper::getValue($value, 'CargoBusinessUnitId', '');

                    $apiLogValue = [
                        'OutboundId' => $OutboundId,
                        'BatchId' => $BatchId,
                        'ReservationId' => $ReservationId,
                        'SkuId' => $SkuId,
                        'Quantity' => $Quantity,
                        'Status' => $Status,
                        'ToBusinessUnitId' => $ToBusinessUnitId,
                        'CargoBusinessUnitId' => $CargoBusinessUnitId,
                    ];


                    foreach($barcodeLots as  $barcode) {
//                            if(!isset($result[$ToBusinessUnitId][$SkuId])) {
                            $result[$ToBusinessUnitId][] = [
//                                $result[$ToBusinessUnitId][$SkuId] = [
                                'client_id' => Client::CLIENT_DEFACTO,
                                'party_number' => $BatchId,
                                'from_point_client_id' => '4',
                                'to_point_client_id' => $ToBusinessUnitId,
                                'order_number' => $ToBusinessUnitId,
                                'status_created_on_client' => $Status,
                                'field_extra1' => $OutboundId,
                                'field_extra2' => $ReservationId,
                                'product_id_on_client' => $SkuId,
//                                'product_barcode' => $this->getProductsBySkuID($SkuId), //$this->getProductIdBySkuID($SkuId), // TODO
                                'product_barcode' => $barcode, //$this->getProductIdBySkuID($SkuId), // TODO
//                                'product_barcode' => $this->getProductIdBySkuID($SkuId), // TODO
                                'expected_qty' => $Quantity,
                                'extra_fields' => Json::encode(['apiLogValue' => $apiLogValue]),
                            ];
//                            } else {
//                                $result[$ToBusinessUnitId][$SkuId]['expected_qty'] += $Quantity;
//                            }
                    }
                }

                file_put_contents('preparedOutboundOrderPartyItemForSaveToDb.log', $OutboundId . ";" . $SkuId . ";" . date('Y-m-d H:i:s') . ";" . "\n", FILE_APPEND);
            }


            $outResult['HasError'] = false;
            $outResult['Data'] = $result;

        } else {
            $outResult['ErrorMessage'] = 'Нет данных для подготовки';
        }

        return $outResult;
    }

    //private $skuIDsWithProducts = [
    //    226794987=>2300018179056,
    //];
	
	    /*
     * Sku from client
     * @return integer
     * */
    public function getProductsBySkuID($skuIdFromClient)
    {
        $lots = [];
        if(isset($this->skuIDsWithProducts[$skuIdFromClient])) {
			return $this->skuIDsWithProducts[$skuIdFromClient];
		}

		$productService = new ProductService();
		$lots = $productService->getBarcodesBySkuId(Client::CLIENT_DEFACTO,$skuIdFromClient);
		if(empty($lots)) {
			$lots = $productService->updateProductBarcodesBySkuId($skuIdFromClient);
		}

		$this->skuIDsWithProducts[$skuIdFromClient] = $lots;

        return $this->skuIDsWithProducts[$skuIdFromClient];
    }

    /*
     * Sku from client
     * @return integer
     * */
    public function getProductsBySkuID_OLD($skuIdFromClient)
    {
        $lots = [];
        if(!isset($this->skuIDsWithProducts[$skuIdFromClient])) {
            $api = new DeFactoSoapAPIV2();
            $dataFromAPI = $api->getMasterData($skuIdFromClient);
            if (!$dataFromAPI['HasError']) {
                if (!empty($dataFromAPI['Data'])) {
                    $resultDataArray = $dataFromAPI['Data'];

                    $resultDataArray = count($resultDataArray) == 1 ? [$resultDataArray] : $resultDataArray;
                    foreach ($resultDataArray as $resultData) {
//                       return $this->skuIDsWithProducts[$skuIdFromClient] = $resultData->LotOrSingleBarcode;
//                       return $this->skuIDsWithProducts[$skuIdFromClient] = [$resultData->LotOrSingleBarcode];
                        $lots[] = $resultData->LotOrSingleBarcode;
                    }
                }
            }
            return $this->skuIDsWithProducts[$skuIdFromClient] = $lots;
        }
        return $this->skuIDsWithProducts[$skuIdFromClient];
    }

     /*
     * Sku from client
     * @return integer
     * */
    public function getProductIdBySkuID($skuFromClient = '')
    {

//        if(substr($skuFromClient,0,3) == '222') {
//            $kzk3 = [];
//            $pathToCSVFile = 'tmp-file/defacto/16-11-2016/returnCSV.csv';
//            if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
//                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//                    $key = substr($data['1'],0,strlen($data['1'])-1);//
//                    $kzk3[$key] = $data['1'];
//                }
//            }
//            $key = trim('222000'.ltrim($skuFromClient,'222'));
//            if(array_key_exists($key,$kzk3)) {
//                return $kzk3[$key];
//            } else {
//                return $skuFromClient;
//            }
//        }

        $products = \common\modules\product\models\defacto\Products::find()
            ->select('LotOrSingleBarcode')
            ->andWhere(['SkuId' => $skuFromClient])
            ->all();
        $LotOrSingleBarcode = '';
        if ($products) {
            foreach ($products as $product) {
                $LotOrSingleBarcode = $product->LotOrSingleBarcode;
                if (Stock::isFindFreeProduct(Client::CLIENT_DEFACTO, $LotOrSingleBarcode)) {
                    return $LotOrSingleBarcode;
                } else { //todo сделать это нормально засунув в функцию
                    //file_put_contents('getProductIdBySkuID-ERROR.log', $skuFromClient . "\n", FILE_APPEND);

                    $api = new DeFactoSoapAPIV2();
                    $dataFromAPI = $api->getMasterData($skuFromClient);
                    if (!$dataFromAPI['HasError']) {
                        usleep(50000);
                        if (!empty($dataFromAPI['Data'])) {
                            $resultDataArray = $dataFromAPI['Data'];

                            $resultDataArray = count($resultDataArray) == 1 ? [$resultDataArray] : $resultDataArray;
                            foreach ($resultDataArray as $resultData) {
                                $LotOrSingleBarcode = $resultData->LotOrSingleBarcode;

                                if (!Products::isExists($resultData->SkuId, $resultData->LotOrSingleBarcode, $resultData->ShortCode)) {
//                                    Products::create($resultData->SkuId, $resultData->LotOrSingleBarcode, $resultData->ShortCode, $resultData->Color);
                                    Products::createByAttributes($resultData);
                                }

                                if (Stock::isFindFreeProduct(Client::CLIENT_DEFACTO, $resultData->LotOrSingleBarcode)) {
                                    return $resultData->LotOrSingleBarcode;
                                } else {
                                    file_put_contents('getProductIdBySkuID-ERROR.log', $skuFromClient . ";" . "\n", FILE_APPEND);
                                }
                            }
                            return $LotOrSingleBarcode;
                        }
                    } else {
                        $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
                    }
                }
            }
            return $LotOrSingleBarcode;
        } else {
            $api = new DeFactoSoapAPIV2();
            $dataFromAPI = $api->getMasterData($skuFromClient);
            if (!$dataFromAPI['HasError']) {
                usleep(50000);
                if (!empty($dataFromAPI['Data'])) {
                    $resultDataArray = $dataFromAPI['Data'];

                    $resultDataArray = count($resultDataArray) == 1 ? [$resultDataArray] : $resultDataArray;
                    foreach ($resultDataArray as $resultData) {
                        $LotOrSingleBarcode = $resultData->LotOrSingleBarcode;

                        if (!Products::isExists($resultData->SkuId, $resultData->LotOrSingleBarcode, $resultData->ShortCode)) {
                            //Products::create($resultData->SkuId, $resultData->LotOrSingleBarcode, $resultData->ShortCode, $resultData->Color);
                            Products::createByAttributes($resultData);
                        }

                        if (Stock::isFindFreeProduct(Client::CLIENT_DEFACTO, $resultData->LotOrSingleBarcode)) {
                            return $resultData->LotOrSingleBarcode;
                        } else {
                            file_put_contents('getProductIdBySkuID-ERROR.log', $skuFromClient . ";" . "\n", FILE_APPEND);
                        }
                    }
                    return $LotOrSingleBarcode;
                }
            } else {
                $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
            }
        }

        return 0;
    }

    private function createProductLot($skuFromClient)
    {

        return 1;
    }


    /*
    * Получаем данне по апи и сохраняем их в таблицы с приходными накладными
    * @param array $data
    * */
    public function saveOutboundOrderPartyItemsToDb($data)
    {
        $outResult = $this->_outResult;
        $id = $this->getConsignmentUniversalId();

//        VarDumper::dump($data,10,true);
//        die('-END-');
        if (!empty($data)) {
            $cu = ConsignmentUniversal::findOne([
                'id' => $id,
                'status_created_on_client' => [DeFactoSoapAPIV2::OUTBOUND_STATUS_NEW, DeFactoSoapAPIV2::OUTBOUND_STATUS_DATA_IS_PREPARED],
                'order_type' => ConsignmentUniversal::ORDER_TYPE_OUTBOUND
            ]);
            if ($cu) {
                ConsignmentUniversalOrdersItems::deleteAll(['consignment_universal_id' => $id]);
                foreach ($data as $apiOrderNumber => $apiOrderData) {


                    $cuOrder = $this->addConsignmentUniversalOrder($cu, $apiOrderNumber);

                    foreach ($apiOrderData as $value) {
                        $value['consignment_universal_id'] = $id;
                        $value['consignment_universal_order_id'] = $cuOrder->id;

//                        $inItem = ConsignmentUniversalOrdersItems::findOne([
//                                'consignment_universal_id' => $id,
//                                'consignment_universal_order_id' => $cuOrder->id,
//                                'from_point_client_id' => $value['from_point_client_id'], // наш стлад
//                                'to_point_client_id' => $value['to_point_client_id'],
//                                'product_barcode' => $value['product_barcode'],
//                                'product_id_on_client' => $value['product_id_on_client'],
//                                'expected_qty' => $value['expected_qty'],
//                            ]
//                        );

//                        if (!$inItem) {
                        $inItem = new ConsignmentUniversalOrdersItems();
//                        }

                        $inItem->setAttributes($value, false);
                        $inItem->save(false);
                    }
                }
                $outResult['HasError'] = false;
                $outResult['Message'] = 'Данные в базу успешно сохранены';
            } else {
                $outResult['ErrorMessage'] = 'Накладная с id ' . $id . '  и статусом ' . DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED . ' не найдена. возможно данные еще не подготовлены. Попробуйте снова через 5 мин';
            }
        } else {
            $outResult['ErrorMessage'] = 'Нет данных для сохранения в базу';
        }
        return $outResult;
    }

    private function addConsignmentUniversalOrder($cu, $apiOrderNumber)
    {
        $cuOrder = ConsignmentUniversalOrders::findOne([
            'consignment_universal_id' => $cu->id,
            'order_number' => $apiOrderNumber,
            'client_id' => $cu->client_id,
            'order_type' => ConsignmentUniversal::ORDER_TYPE_OUTBOUND
        ]);

        if (!$cuOrder) {
            $cuOrder = new ConsignmentUniversalOrders();
            $cuOrder->consignment_universal_id = $cu->id;
            $cuOrder->client_id = $cu->client_id;
            $cuOrder->status = Stock::STATUS_OUTBOUND_NEW;
            $cuOrder->order_number = $apiOrderNumber;
            $cuOrder->party_number = $cu->party_number;
            $cuOrder->order_type = ConsignmentUniversal::ORDER_TYPE_OUTBOUND;
            $cuOrder->expected_qty = 0;
            $cuOrder->accepted_qty = 0;
            $cuOrder->save(false);
        }

        $cuOrder->expected_qty = 0;
        $cuOrder->accepted_qty = 0;
        $cuOrder->save(false);
        return $cuOrder;
    }


    /* Подготоваливаем данные полученые по апи от клиента с помощью GetWarehouseAppointments для сохранения в нашу базу
    * @param array stdClass $data. Example values:
    * [BatchId] => 82
    * [BusinessUnitId] => 1029
    * [Status] => 'OutBoundDataIsPrepared'
    * @return array
    * */
    private function preparedOutboundOrderPartyForSaveToDb($data) // OK
    {
        $outResult = $this->_outResult;
        $result = [];
        if (!empty($data)) {
            foreach ($data as $value) {
                $BatchId = ArrayHelper::getValue($value, 'BatchId', '');
                $BusinessUnitId = ArrayHelper::getValue($value, 'BusinessUnitId', '');
                $Status = ArrayHelper::getValue($value, 'Status', '');

                $apiLogValue = [
                    'BatchId' => $BatchId,
                    'BusinessUnitId' => $BusinessUnitId,
                    'Status' => $Status,
                ];

                $result[] = [
                    'client_id' => Client::CLIENT_DEFACTO,
                    'party_number' => $BatchId,
//                    'status'=>ConsignmentUniversal::STATUS_OUTBOUND_NEW,
                    'status_created_on_client' => $Status,
                    'order_type' => ConsignmentUniversal::ORDER_TYPE_OUTBOUND,
                    'extra_fields' => Json::encode(['apiLogValue' => $apiLogValue]),
                ];
            }

            $outResult['HasError'] = false;
            $outResult['Data'] = $result;

        } else {
            $outResult['ErrorMessage'] = 'Нет данных для подготовки';
        }
        return $outResult;
    }

    /*
    * @param array $data
    * */
    private function saveOutboundOrderPartyToDb($data) // OK
    {
        $outResult = $this->_outResult;
        if (!empty($data)) {
            //ConsignmentUniversal::deleteAll(['order_type'=>ConsignmentUniversal::ORDER_TYPE_OUTBOUND]);
            foreach ($data as $value) {
                $cu = ConsignmentUniversal::findOne([
                    'party_number' => $value['party_number'],
                    'client_id' => $value['client_id'],
                    'order_type' => ConsignmentUniversal::ORDER_TYPE_OUTBOUND,
                ]);
                if (!$cu) {
                    $cu = new ConsignmentUniversal();
                    $value['status'] = ConsignmentUniversal::STATUS_OUTBOUND_NEW;
                }

//                if($value['status_created_on_client'] == DeFactoSoapAPIV2::OUTBOUND_STATUS_NEW) {
//                    $value['status'] = ConsignmentUniversal::STATUS_OUTBOUND_NEW;
//                }

                $cu->setAttributes($value, false);
                $cu->save(false);
            }
            $outResult['HasError'] = false;
            $outResult['Message'] = 'Данные в базу успешно сохранены';
        } else {
            $outResult['ErrorMessage'] = 'Нет данных для сохранения в базу';
        }
        return $outResult;
    }

    /*
    * Уведомляем по средством апи сторону девакта что заданная приходная накладная прибыла к нам на склад. Т.е грузовик приехал к нам на склад но мы его еще не начали разгружать.
    * @param string $id
    * */
    public function saveMarkOutboundPartyById() // OK
    {
        $outResult = $this->_outResult;

        $id = $this->getConsignmentUniversalId();

        if ($cu = ConsignmentUniversal::findOne(['id' => $id, 'status_created_on_client' => [DeFactoSoapAPIV2::OUTBOUND_STATUS_NEW, DeFactoSoapAPIV2::OUTBOUND_STATUS_DATA_IS_PREPARED]])) {
            $dataFromAPI = $this->sendMarkOutboundParty($cu->party_number);
            if (!$dataFromAPI['HasError']) {
                $cu->status = self::OUTBOUND_STATUS_MARKED_FOR_OUTBOUND_DATA;
                $cu->save(false);

                $outResult['HasError'] = false;
                $outResult['Message'] = 'Данные подготавливаются. Пожалуйста подождите 15 мин';
            } else {
                $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
            }

        } else {
            $outResult['ErrorMessage'] = 'Накладная с id ' . $id . '  и статусом NOTHING не найдена';
        }
        return $outResult;
    }

    /*
    * Уведомляем по средством апи сторону дефакта что заданная расходная накладная готова для сборки на складе.
    * @param integer $pickingID
    * */
    private function sendMarkOutboundParty($pickingID) // OK
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->MarkPickingForOutbound($pickingID);
        if (!$dataFromAPI['HasError']) {
            $outResult['HasError'] = false;
            $outResult['Message'] = 'Данные успешно переданы дефакто';
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }
        return $outResult;
    }

    /*
     * Сохраняем данные о приходе. Те товары которые нужно принят на склад
     * */
    public function saveOutboundInStockToDb() // OK
    {
        $outResult = $this->_outResult;
        $id = $this->getConsignmentUniversalId();
        $cu = ConsignmentUniversal::find()
            ->andWhere([
                        'id' => $id,
                        'status_created_on_client' => [
                            DeFactoSoapAPIV2::OUTBOUND_STATUS_NEW,
                            DeFactoSoapAPIV2::OUTBOUND_STATUS_DATA_IS_PREPARED
                        ]
            ])
            ->andWhere(['!=','status',ConsignmentUniversal::STATUS_OUTBOUND_SAVED_AND_CREATE_ORDERS])
            ->one();

        if (!$cu) {
            $outResult['ErrorMessage'] = "Накладная не найдена";
            return $outResult;
        }

        $inOutbound = OutboundOrder::find()->andWhere(['parent_order_number'=>$cu->party_number])->exists();
        if ($inOutbound) {
            $outResult['ErrorMessage'] = "Накладная не найдена";
            return $outResult;
        }

        ConsignmentUniversal::setOutboundStatus($id,ConsignmentUniversal::STATUS_OUTBOUND_SAVED_AND_CREATE_ORDERS);

        $lotQty = 0;
        $cuOrders = ConsignmentUniversalOrders::findAll(['consignment_universal_id' => $cu->id, 'client_id' => $cu->client_id]);
        if ($cuOrders) {
            foreach ($cuOrders as $cuOrder) {
                $cuoItems = ConsignmentUniversalOrdersItems::findAll(['consignment_universal_id' => $cu->id, 'consignment_universal_order_id' => $cuOrder->id]);
                $outboundDataToSave = [];
                if (!empty($cuoItems)) {
                    foreach ($cuoItems as $item) {
                        $outboundDataToSave [] = [
                            'product_barcode' => $item['product_barcode'],
                            'product_id_on_client' => $item['product_id_on_client'],
                            'expected_qty' => $item['expected_qty'],
                            'status' => Stock::STATUS_OUTBOUND_NEW,
                            'product_serialize_data' => Json::encode($item),
                            'consignment_universal_orders_item_id' => $item['id'],
                        ];
                        $lotQty += $item['expected_qty'];
                    }
                }
                if (!empty($outboundDataToSave)) {
                    $con = ConsignmentOutboundOrder::findOne([
                        'party_number' => $cuOrder->party_number,
                        'client_id' => $cuOrder->client_id]);

                    if (!$con) {
                        $con = new ConsignmentOutboundOrder();
                    }

                    $con->client_id = $cu->client_id;
                    $con->status = Stock::STATUS_OUTBOUND_NEW;
                    $con->party_number = $cuOrder->party_number;
                    $con->expected_qty = 0;
                    $con->accepted_qty = 0;
                    $con->save(false);

                    $in = $this->addOutbound($cuOrder, $con);
                    // Добавляем линии с товарами
                    $this->addOutboundItem($outboundDataToSave, $in, $con);
                }
            }

            $outResult['HasError'] = false;
            $outResult['ErrorMessage'] = "";
            $outResult['Message'] = "Накладная успешно загружена";
        }
        $cu->expected_qty = $lotQty;
        $cu->save(false);

        return $outResult;
    }

    /*
    *
    * */
    private function addOutbound($cuOrder, $con)
    {
        $in = OutboundOrder::findOne([
            'consignment_outbound_order_id' => $con->id,
            'parent_order_number' => $cuOrder->party_number,
            'order_number' => $cuOrder->order_number,
            'client_id' => $cuOrder->client_id]);
        if (!$in) {
            $in = new OutboundOrder();
        }

        $in->from_point_id = 4;
        $in->to_point_id = $this->getStore($cuOrder->order_number);
        $in->to_point_title = $cuOrder->order_number;
        $in->consignment_outbound_order_id = $con->id;
        $in->client_id = $cuOrder->client_id;
        $in->status = Stock::STATUS_OUTBOUND_NEW;
        $in->order_number = $cuOrder->order_number;
        $in->parent_order_number = $cuOrder->party_number;
        $in->expected_qty = 0;
        $in->accepted_qty = 0;
        $in->save(false);


        if ($in) {

            $outboundModel = $in;
            $update = false;

            if ($dp = TlDeliveryProposal::find()
                ->andWhere([
                    'route_from' => $outboundModel->from_point_id,
                    'route_to' => $outboundModel->to_point_id,
                    'client_id' => $outboundModel->client_id,
                    'status' => [TlDeliveryProposal::STATUS_NEW]
                ])->one()
            ) {

                if (!($dpOrder = TlDeliveryProposalOrders::findOne(['client_id' => $outboundModel->client_id, 'order_id' => $outboundModel->id, 'order_type' => TlDeliveryProposalOrders::ORDER_TYPE_RPT, 'order_number' => $outboundModel->parent_order_number . ' ' . $outboundModel->order_number]))) {
                    $dpOrder = new TlDeliveryProposalOrders();
                }
                $update = true;
            } else {
                $dp = new TlDeliveryProposal();
//                $dpOrder = new TlDeliveryProposalOrders();
                if (!($dpOrder = TlDeliveryProposalOrders::findOne(['client_id' => $outboundModel->client_id, 'order_id' => $outboundModel->id, 'order_type' => TlDeliveryProposalOrders::ORDER_TYPE_RPT, 'order_number' => $outboundModel->parent_order_number . ' ' . $outboundModel->order_number]))) {
                    $dpOrder = new TlDeliveryProposalOrders();
                }
            }


            $dp->status = TlDeliveryProposal::STATUS_NEW;
            $dp->client_id = $outboundModel->client_id;
            $dp->route_from = $outboundModel->from_point_id;
            $dp->route_to = $outboundModel->to_point_id;
            $dp->cash_no = TlDeliveryProposal::METHOD_CHAR;
            $dp->save(false);

            // Добавить заказы
            if($dpOrder) {
                $dpOrder->client_id = $dp->client_id;
                $dpOrder->tl_delivery_proposal_id = $dp->id;
                $dpOrder->order_id = $outboundModel->id;
                $dpOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
                $dpOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
                $dpOrder->order_number = $outboundModel->parent_order_number . ' ' . $outboundModel->order_number;
                $dpOrder->kg = $outboundModel->kg;
                $dpOrder->kg_actual = $outboundModel->kg;
                $dpOrder->mc = $outboundModel->mc;
                $dpOrder->mc_actual = $outboundModel->mc;
                $dpOrder->number_places = $outboundModel->accepted_number_places_qty;
                $dpOrder->number_places_actual = $outboundModel->accepted_number_places_qty;
                $dpOrder->title = $outboundModel->title;
                $dpOrder->description = $outboundModel->description;
                $dpOrder->save(false);
            }

            $dpManager = new DeliveryProposalManager(['id' => $dp->id]);

            if ($update) {
                $dpManager->onUpdateProposal();
            } else {
                $dpManager->onCreateProposal();
            }
        }

        return $in;
    }

    /*
     *
     * */
    private function addOutboundItem($outboundDataToSave, $in, $con)
    {
        // Добавляем линии с товарами
        OutboundOrderItem::deleteAll(['outbound_order_id' => $in->id]);
        $expectedQty = 0;
        foreach ($outboundDataToSave as $item) {
            $attribute = [
                'outbound_order_id' => $in->id,
                'product_barcode' => $item['product_barcode'],
                'expected_qty' => $item['expected_qty'],
                'status' => $item['status'],
                'product_serialize_data' => $item['product_serialize_data'],
                'field_extra1' => $item['product_id_on_client'],
            ];
            $inItem = new OutboundOrderItem();
            $inItem->setAttributes($attribute, false);
            $inItem->save(false);

            $expectedQty += $item['expected_qty'];

        }
        $in->expected_qty = $expectedQty;
//        $in->expected_qty = $expectedQty / 2;
        $in->save(false);
        //sleep(5);
        // Reservation on stock
//        if ($oos = OutboundOrder::find()->select('id')->andWhere(['consignment_outbound_order_id' => $con->id])->asArray()->all()) {
//            foreach ($oos as $order) {
//                Stock::resetByOutboundOrderId($order['id']);
//                Stock::AllocateByOutboundOrderId($order['id']);
//            }
//        }
        return 0;
    }

    /*
     *
     * */
    public function getStore($clientStoreId)
    {
        $client_id = Client::CLIENT_DEFACTO;
        if ($point = Store::find()->andWhere(['client_id' => $client_id, 'shop_code3' => $clientStoreId])->one()) {
            return $point->id;
        }

        if ($point = Store::find()->andWhere(['client_id' => $client_id, 'shop_code2' => $clientStoreId])->one()) {
            return $point->id;
        }
        // для нашего склада
        if ($point = Store::find()->andWhere(['client_id' => 3, 'shop_code3' => $clientStoreId])->one()) {
            return $point->id;
        }

        return 0;
    }

    /*
* @param CrossDockItems $item
* */
    public static function preparedSendCrossDockOutBoundFeedBackDataInbound($item)
    {
        $jsonProductData = [];
        echo "Start preparedSendCrossDockOutBoundFeedBackData:" . "<br />";
        if (!empty($item->product_serialize_data)) {
            $jsonProductData = Json::decode($item->product_serialize_data);
            $jsonProductDataLet = [];
            if ($extraFieldLet = ArrayHelper::getValue($jsonProductData, 'extra_fields')) {
                $jsonProductDataLet = Json::decode($extraFieldLet);
            }
            $jsonProductData['extra_fields'] = $jsonProductDataLet;
        }

        $InBoundId = $item->field_extra1;
        $LcOrCartonLabel = $item->box_barcode;
        $LotOrSingleBarcode = $item->field_extra2;
        $AppointmentBarcode = ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.AppointmentBarcode');;
        $LotOrSingleQuantity = 1; // TODO ?
        $row = [];
        $row[] = [
            'InboundId' => $InBoundId,//'48', // если плюсы то передаем null
            'AppointmentBarcode' => $AppointmentBarcode,//'D10AA00000043',
            'LcOrCartonLabel' => $LcOrCartonLabel, //'2430000072423',
            'LotOrSingleBarcode' => $LotOrSingleBarcode, //'9000003635927',
            'LotOrSingleQuantity' => $LotOrSingleQuantity, //'1',
			'IsDamaged' => false
        ];
        return $row;
    }


    /*
    * @param CrossDockItems $item
    * */
    public static function preparedSendCrossDockOutBoundFeedBackDataOutbound($item, $crossDock)
    {
        $jsonProductData = [];
//        echo "Start preparedSendCrossDockOutBoundFeedBackData:"."<br />";
        if (!empty($item->product_serialize_data)) {
            $jsonProductData = Json::decode($item->product_serialize_data);
            $jsonProductDataLet = [];
            if ($extraFieldLet = ArrayHelper::getValue($jsonProductData, 'extra_fields')) {
                $jsonProductDataLet = Json::decode($extraFieldLet);
            }
            $jsonProductData['extra_fields'] = $jsonProductDataLet;
        }

        $OutBoundId = '';
        $InBoundId = $item->field_extra1;
        $LcBarcode = $item->box_barcode;
        $LotOrSingleBarcode = $item->field_extra2;
        $Volume = BarcodeManager::getIntegerM3($item->box_m3);
        $InvoiceNumber = ltrim($crossDock->internal_barcode, '2-');// $AppointmentBarcode = ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.AppointmentBarcode');;
        $SkuQuantity = ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.LotOrSingleQuantity');
//        $SkuQuantity = 1;
        $WaybillSerial = 'KZK';
        $CargoShipmentNo = '1';
        $WaybillNumber = '1'; //TODO Если я сюда ставлю что-то отличное от единицы получаю ошибку.
        $row = [];
        $row[] = [
            'OutBoundId' => $OutBoundId,//'16', // это ид из SendOutBoundFeedBackData
            'InBoundId' => $InBoundId,//'0', // Что это такое ?
            'LcBarcode' => $LcBarcode,// OLD PackBarcode '700000001', // наш короб
            'LotOrSingleBarcode' => $LotOrSingleBarcode,// OLD SkuBarcode '8680654893689', // Что это такое ?
            'LotOrSingleQuantity' => $SkuQuantity,//OLD SkuQuantity '12', // кол-во лотов в коробе
            'WaybillSerial' => $WaybillSerial,//'KZK', //это не меняется
            'WaybillNumber' => $WaybillNumber,//'16', //ReservationId - брать из GetWarehousePickings (его тут нет) и он пуст в GetPickingOutBoundData
            'Volume' => (!empty($Volume) ? $Volume : 32 ),//'32', //размер короба это mapM3ToBoxSize 12, 17, 31 и т.д.
            'CargoShipmentNo' => $CargoShipmentNo,//'-', //не используем
            'InvoiceNumber' => $InvoiceNumber,//это номер приходной накладно по которой мы приняли этот товар
        ];
        return $row;
    }


    /*
     *
     * */
    public function SendOutBoundCrossDockFeedBackData($data) //
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->SendOutBoundFeedBackData($data);
//        $dataFromAPI = $api->SendInBoundFeedBackData($data);
        if (!$dataFromAPI['HasError']) {
            $outResult['HasError'] = false;
            $outResult['Message'] = 'Данные успешно переданы дефакто';
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }
        return $outResult;
    }

    /*
     *
     * */
    public function SendOutBoundCrossDockFeedBackDataOutbound($data) //
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->SendOutBoundFeedBackData($data);
//        $dataFromAPI = $api->SendInBoundFeedBackData($data);
        if (!$dataFromAPI['HasError']) {
            $outResult['HasError'] = false;
            $outResult['Message'] = 'Данные успешно переданы дефакто';
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }
        return $outResult;
    }

    /*
     *
     * */
    public function CreateLcBarcode($count)
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->createLcBarcode($count);
        if (!$dataFromAPI['HasError']) {
            $outResult['HasError'] = false;
            $outResult['Message'] = 'Штрихкоды от дефакто успешно получены';
            $outResult['Data'] = $dataFromAPI['Data'];
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }
        return $outResult;

    }

    /*
    * @param integer $orderType
    * */
    public function getConsignmentUniversalActiveDataProvider($orderType = ConsignmentUniversal::ORDER_TYPE_INBOUND)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ConsignmentUniversal::find()
                ->select('id, party_number, status_created_on_client, data_created_on_client, extra_fields, field_extra1, status, expected_qty, expected_number_places_qty ')
				 ->andWhere('status != 12')
                ->andWhere(['order_type' => $orderType, 'client_id' => Client::CLIENT_DEFACTO])
                ->andWhere('status_created_on_client != :status_created_on_client', [':status_created_on_client' => DeFactoSoapAPIV2::INBOUND_STATUS_COMPLETED])
                ->orderBy(['status_created_on_client' => SORT_DESC]),
            'pagination' => false,
            'sort' => false,
        ]);
        return $dataProvider;
    }

    /*
    * @param
    * */
    public function getNewInboundsForNotify()
    {
        return ConsignmentUniversal::find()
            ->select('party_number, field_extra1, id, status_created_on_client, data_created_on_client, extra_fields,  status')
            ->andWhere(['order_type' => ConsignmentUniversal::ORDER_TYPE_INBOUND, 'client_id' => Client::CLIENT_DEFACTO, 'status' => ConsignmentUniversal::STATUS_INBOUND_NEW])
            ->andWhere('status_created_on_client = :status_created_on_client', [':status_created_on_client' => DeFactoSoapAPIV2::INBOUND_STATUS_NOTHING])
            ->andWhere(['status_notification' => ConsignmentUniversal::STATUS_NOTIFICATION_DEFAULT])
            ->orderBy(['status_created_on_client' => SORT_DESC])
            ->all();
    }

    /*
    * @param
    * */
    public function getPreparedInboundsForNotify()
    {
        return ConsignmentUniversal::find()
            ->select('party_number, field_extra1, id, status_created_on_client, data_created_on_client, extra_fields,  status')
            ->andWhere(['order_type' => ConsignmentUniversal::ORDER_TYPE_INBOUND, 'client_id' => Client::CLIENT_DEFACTO, 'status' => ConsignmentUniversal::STATUS_INBOUND_NEW])
            ->andWhere('status_created_on_client = :status_created_on_client', [':status_created_on_client' => DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED])
            ->andWhere(['status_notification' => ConsignmentUniversal::STATUS_NOTIFICATION_NEW_INBOUND])
            ->orderBy(['status_created_on_client' => SORT_DESC])
            ->all();
    }


    /*
    * @param
    * */
    public function getNewOutboundsForNotify()
    {
        return ConsignmentUniversal::find()
            ->select('party_number, field_extra1, id, status_created_on_client, data_created_on_client, extra_fields,  status')
            ->andWhere(['order_type' => ConsignmentUniversal::ORDER_TYPE_OUTBOUND, 'client_id' => Client::CLIENT_DEFACTO, 'status' => ConsignmentUniversal::STATUS_OUTBOUND_NEW])
            ->andWhere(['status_created_on_client' => DeFactoSoapAPIV2::OUTBOUND_STATUS_DATA_IS_PREPARED])
            ->andWhere(['status_notification' => ConsignmentUniversal::STATUS_NOTIFICATION_DEFAULT])
            ->orderBy(['status_created_on_client' => SORT_DESC])
            ->all();
    }

    /*
     * Get color class by client status
     * @param string $status
     * */
    public static function getColorClassByClientStatus($status)
    {
        switch ($status) {
            case DeFactoSoapAPIV2::INBOUND_STATUS_NOTHING: // Новый
                $class = 'color-indian-red';
                break;
            case DeFactoSoapAPIV2::INBOUND_STATUS_MARKED_FOR_INBOUND_DATA: //Готов к загрузку данных
                $class = 'color-light-yellow';
                break;
            case DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED: //Готов для гагрузки к нам на сервер
                $class = 'color-tan';
                break;
            case DeFactoSoapAPIV2::INBOUND_STATUS_COMPLETED: //Выполнен
                $class = 'color-dark-olive-green';
                break;
            default:
                $class = '';
                break;

        }
        return $class;
    }

    /*
     *
     * */
    public static function preparedSendInBoundFeedBackDataReturn($item) // OK
    {
        $jsonProductData = [];
        if (!empty($item->product_serialize_data)) {
            $jsonProductData = Json::decode($item->product_serialize_data);
//            $jsonProductDataLet = [];
//            if($extraFieldLet =  ArrayHelper::getValue($jsonProductData,'apiLogValue')) {
//                $jsonProductDataLet = Json::decode($extraFieldLet);
//            }
//            $jsonProductData['product_serialize_data'] = $jsonProductData;
        }

//        VarDumper::dump($jsonProductData,10,true);

        $InBoundId = $item->field_extra1;//ArrayHelper::getValue($jsonProductData, 'field_extra1');
        $AppointmentBarcode = ArrayHelper::getValue($jsonProductData, 'apiLogValue.AppointmentBarcode');
        $LcOrCartonLabel = $item->client_box_barcode;//ArrayHelper::getValue($jsonProductData, 'extra_fields.apiLogValue.LcOrCartonLabel');
        $LotOrSingleBarcode = $item->product_barcode;
        $LotOrSingleQuantity = $item->expected_qty;

        return [
            'InboundId' => $InBoundId,//'48', // если плюсы то передаем null
            'AppointmentBarcode' => $AppointmentBarcode,//'D10AA00000043',
            'LcOrCartonLabel' => $LcOrCartonLabel, //'2430000072423',
            'LotOrSingleBarcode' => $LotOrSingleBarcode, //'9000003635927',
            'LotOrSingleQuantity' => $LotOrSingleQuantity, //'1',
			'IsDamaged' => false
        ];
    }

    /**
     * Подсчитываем все ли накладные приняты и если да то закрываем накладную
     * @param string $parentOrderNumber
     * @param string $consignmentOutboundOrderId
     * @return bool
     */
    public static function checkCountCompleteOutboundOrders($parentOrderNumber, $consignmentOutboundOrderId)
    {
        $andWhere = [
            'client_id' => Client::CLIENT_DEFACTO,
            'parent_order_number' => $parentOrderNumber,
            'consignment_outbound_order_id' => $consignmentOutboundOrderId,
        ];

        $count = OutboundOrder::find()->andWhere($andWhere)->count();

        $andWhere['status'] = [
            Stock::STATUS_OUTBOUND_PACKED,
            Stock::STATUS_OUTBOUND_SHIPPING,
            Stock::STATUS_OUTBOUND_SHIPPED,
            Stock::STATUS_OUTBOUND_COMPLETE,
            Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
            Stock::STATUS_OUTBOUND_ON_ROAD,
            Stock::STATUS_OUTBOUND_DELIVERED,
            Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
            Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
        ];

        $countPrepared = OutboundOrder::find()->andWhere($andWhere)->count();

        return $countPrepared == $count;
    }

    /*
    * Получаем список возвратов накладных по апи с созданяем в нащшу базу
    *
    * */
    public function getAndSaveReturnOrderParty()
    {
        $outResult = $this->_outResult;
        $preparedData = $this->getAndPreparedReturnOrderParty();
        if ($preparedData['HasError']) {
            $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
            return $outResult;
        }

        $saveToDbData = $this->saveReturnOrderPartyToDb($preparedData['Data']);
        if ($saveToDbData['HasError']) {
            $outResult['ErrorMessage'] = $saveToDbData['ErrorMessage'];
            return $outResult;
        }

        $outResult['HasError'] = false;
        $outResult['Message'] = $saveToDbData['Message'];
        return $outResult;
    }

    /*
    * Получаем список возвратов накладных по апи с созданяем в нащшу базу
    *
    * */
    public function getAndSaveReturnOrderParty1()
    {
        $outResult = $this->_outResult;
        $preparedData = $this->getAndPreparedReturnOrderParty();
        if ($preparedData['HasError']) {
            $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
            return $outResult;
        }

        $saveToDbData = $this->saveReturnOrderPartyToDb($preparedData['Data'], $preparedData['AppointmentBarcode']);
        if ($saveToDbData['HasError']) {
            $outResult['ErrorMessage'] = $saveToDbData['ErrorMessage'];
            return $outResult;
        }

        $outResult['HasError'] = false;
        $outResult['Message'] = $saveToDbData['Message'];
        return $outResult;
    }

    /*
    *
    * */
    public function getAndPreparedReturnOrderParty()
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->GetInBoundDataForReturn();

        if ($dataFromAPI['HasError']) {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
            return $outResult;
        }

        $preparedData = $this->preparedReturnOrderPartyForSaveToDb($dataFromAPI['Data']);
        if ($preparedData['HasError']) {
            $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
            return $outResult;
        }

        $outResult['HasError'] = false;
        $outResult['Data'] = $preparedData['Data'];
//        $outResult['AppointmentBarcode'] = isset($preparedData['AppointmentBarcode']) ? $preparedData['AppointmentBarcode'] : [];

        return $outResult;
    }

    /*
     *@params array $partyNumbers
     * */
    public function saveReturnOrderPartyToDb($dataAPI)
    {
        $outResult = $this->_outResult;
        if (empty($dataAPI)) {
            $outResult['ErrorMessage'] = 'Нет данных для сохранения в базу';
            return $outResult;
        }

        $consignmentUniversal = ConsignmentUniversal::findOne(399);
        $expectedQty = 0;

        foreach ($dataAPI as $boxBarcodeClient => $product) {
            $exist = ConsignmentUniversalOrdersItems::find()->andWhere([
                'from_point_client_id'=>$product['from_point_client_id'],
                'to_point_client_id'=>$product['to_point_client_id'],
                'box_barcode_client'=>$product['box_barcode_client'],
                'field_extra1'=>$product['field_extra1'],
                'product_barcode'=>$product['product_barcode'],
                'order_type'=>ConsignmentUniversal::ORDER_TYPE_RETURN,
//                'status'=>ConsignmentUniversal::STATUS_RETURN_NEW,
            ])->exists();

            file_put_contents("saveReturnOrderPartyToDbDot.log",'.'."\n",FILE_APPEND);
            file_put_contents("saveReturnOrderPartyToDb.log",print_r($product,true)."\n",FILE_APPEND);
            if(!$exist) {
                $expectedQty += $product['expected_qty'];
            }
        }

        $consignmentUniversal->expected_qty = $expectedQty;
        $consignmentUniversal->save(false);

        $outResult['HasError'] = false;
        $outResult['Message'] = 'Данные в базу успешно сохранены  в кол-ве: '.$expectedQty;
        return $outResult;
    }

    /*
     * */
    public function saveReturnOrderPartyToDbOLD($dataAPI, $partyNumbers = [])
    {
        $outResult = $this->_outResult;
        if (empty($dataAPI)) {
            $outResult['ErrorMessage'] = 'Нет данных для сохранения в базу';
            return $outResult;
        }

        foreach ($partyNumbers as $partyNumber) {
            $consignmentUniversal = ConsignmentUniversal::findOne([
                'party_number' => $partyNumber,
                'order_type' => ConsignmentUniversal::ORDER_TYPE_RETURN
            ]);

            if (!$consignmentUniversal) {
                $consignmentUniversal = new ConsignmentUniversal();
                $consignmentUniversal->client_id = Client::CLIENT_DEFACTO;
                $consignmentUniversal->order_type = ConsignmentUniversal::ORDER_TYPE_RETURN;
                $consignmentUniversal->party_number = $partyNumber;
                $consignmentUniversal->status = ConsignmentUniversal::STATUS_RETURN_LOADED_FROM_API;
                $consignmentUniversal->save(false);
            }

            $expectedQty = 0;
            $boxBarcodeClientForQty = [];

            foreach ($dataAPI as $boxBarcodeClient => $productsInBox) {
                foreach ($productsInBox as $product) {
                    if (!in_array($boxBarcodeClient, $boxBarcodeClientForQty) && $product['order_number'] == $partyNumber) {
                        $boxBarcodeClientForQty[] = $boxBarcodeClient;
                    }

                    if ($product['order_number'] == $partyNumber) {
                        $expectedQty += $product['expected_qty'];
                    }
                }
            }

            $consignmentUniversal->expected_qty = $expectedQty;
            $consignmentUniversal->expected_number_places_qty = count($boxBarcodeClientForQty);
            $consignmentUniversal->save(false);
        }

        $outResult['HasError'] = false;
        $outResult['Message'] = 'Данные в базу успешно сохранены';
        return $outResult;
    }

    /*
     *@params array $partyNumbers
     * */
    public function saveConsignmentUniversalReturnOrderItemsToDb($id)
    {
        $outResult = $this->_outResult;
        $consignmentUniversal = ConsignmentUniversal::findOne($id);

        if (!$consignmentUniversal) {
            $outResult['ErrorMessage'] = 'Нет данных для сохранения в базу';
            return $outResult;
        }

        $preparedData = $this->getAndPreparedReturnOrderParty();
        if ($preparedData['HasError']) {
            $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
            return $outResult;
        }

        $dataAPI = $preparedData['Data'];

        foreach ($dataAPI as $boxBarcodeClient => $product) {

                $itemAttributes = [];
                $itemAttributes['consignment_universal_id'] = $consignmentUniversal->id;
                $itemAttributes['consignment_universal_order_id'] = 0;
                $itemAttributes['client_id'] = $consignmentUniversal->client_id;
                $itemAttributes['party_number'] = $consignmentUniversal->party_number;
                $itemAttributes['box_barcode_client'] = $product['box_barcode_client'];
                $itemAttributes['product_barcode'] = $product['product_barcode'];
                $itemAttributes['expected_qty'] = $product['expected_qty'];
                $itemAttributes['field_extra1'] = $product['field_extra1'];
                $itemAttributes['extra_fields'] = $product['extra_fields'];
                $itemAttributes['from_point_client_id'] = $product['from_point_client_id'];
                $itemAttributes['from_point_id'] = $product['from_point_id'];
                $itemAttributes['to_point_client_id'] = $product['to_point_client_id'];
                $itemAttributes['to_point_id'] = $product['to_point_id'];
                $itemAttributes['status'] = ConsignmentUniversal::STATUS_RETURN_NEW;
                $itemAttributes['order_type'] =ConsignmentUniversal::ORDER_TYPE_RETURN;


            $exist = ConsignmentUniversalOrdersItems::find()->andWhere([
                'from_point_client_id'=>$product['from_point_client_id'],
                'to_point_client_id'=>$product['to_point_client_id'],
                'box_barcode_client'=>$product['box_barcode_client'],
                'field_extra1'=>$product['field_extra1'],
                'product_barcode'=>$product['product_barcode'],
                'order_type'=>ConsignmentUniversal::ORDER_TYPE_RETURN,
            ])->exists();

                if (!$exist) {
                    $inItem = new ConsignmentUniversalOrdersItems();
                    $inItem->setAttributes($itemAttributes, false);
                    $inItem->save(false);
                }
        }

        $outResult['HasError'] = false;
        $outResult['Message'] = 'Данные в базу успешно сохранены';

        return $outResult;
    }


    /* Подготоваливаем данные полученые по апи от клиента с помощью GetWarehouseAppointments для сохранения в нашу базу
    * @param array stdClass $data. Example values:
    * [Id] => 1514
    * [FromBusinessUnitId] => 835
    * [LcOrCartonLabel] => '2430000013394'
    * [NumberOfCartons] => '1.00'
    * [SkuId] => 2060081
    * [LotOrSingleBarcode] => '8681228230930'
    * [LotOrSingleQuantity] => '1.00'
    * [Status] => 'ReadyforProcessing'
    * [AppointmentBarcode] => 'D10AA00000090'
    * [ToBusinessUnitId] => 1029
    * [FlowType] => 'MixReturn'
    * @return array
    * */
    public function preparedReturnOrderPartyForSaveToDb($data)
    {
        $outResult = $this->_outResult;
        $result = [];
        if (empty($data)) {
            $outResult['ErrorMessage'] = 'Нет данных для подготовки';
            return $outResult;
        }
//        $iForDebug = 0;// only for fix
//        $iForDebugStart = 0;// only for fix
//        $iForDebugEnd = 100;// only for fix
        foreach ($data as $value) {
//            ++$iForDebug;
//            $LcOrCartonLabel = ArrayHelper::getValue($value, 'LcOrCartonLabel', '');
//            file_put_contents('A.log',$LcOrCartonLabel."\n",FILE_APPEND);
//            file_put_contents('preparedReturnOrderPartyForSaveToDb.log','1'."\n",FILE_APPEND);
//            if($iForDebug < $iForDebugStart) {
//                file_put_contents('preparedReturnOrderPartyForSaveToDb.log','2'."\n",FILE_APPEND);
//                continue;
//            }

            $Id = ArrayHelper::getValue($value, 'Id', '');
            $FromBusinessUnitId = ArrayHelper::getValue($value, 'FromBusinessUnitId', '');
            $LcOrCartonLabel = ArrayHelper::getValue($value, 'LcOrCartonLabel', '');
            $NumberOfCartons = ArrayHelper::getValue($value, 'NumberOfCartons', '');
            $SkuId = ArrayHelper::getValue($value, 'SkuId', '');
            $LotOrSingleBarcode = ArrayHelper::getValue($value, 'LotOrSingleBarcode', '');
            $LotOrSingleQuantity = ArrayHelper::getValue($value, 'LotOrSingleQuantity', '');
            $Status = ArrayHelper::getValue($value, 'Status', '');
            $AppointmentBarcode = ArrayHelper::getValue($value, 'AppointmentBarcode', '');
            $ToBusinessUnitId = ArrayHelper::getValue($value, 'ToBusinessUnitId', '');
            $FlowType = ArrayHelper::getValue($value, 'FlowType', '');

            $apiLogValue = [
                'Id' => $Id,
                'FromBusinessUnitId' => $FromBusinessUnitId,
                'LcOrCartonLabel' => $LcOrCartonLabel,
                'NumberOfCartons' => $NumberOfCartons,
                'SkuId' => $SkuId,
                'LotOrSingleBarcode' => $LotOrSingleBarcode,
                'LotOrSingleQuantity' => $LotOrSingleQuantity,
                'Status' => $Status,
                'AppointmentBarcode' => $AppointmentBarcode,
                'ToBusinessUnitId' => $ToBusinessUnitId,
                'FlowType' => $FlowType,
            ];
            $result[] = [
                'client_id' => Client::CLIENT_DEFACTO,
                'order_number' => $AppointmentBarcode,
                'from_point_client_id' => $FromBusinessUnitId,
                'from_point_id' => $this->getStore($FromBusinessUnitId),
                'to_point_client_id' => $ToBusinessUnitId,
                'to_point_id' => $this->getStore($ToBusinessUnitId),
                'box_barcode_client' => $LcOrCartonLabel,
                'status_created_on_client' => $Status,
                'order_type' => ConsignmentUniversal::ORDER_TYPE_RETURN,
                'product_barcode' => $LotOrSingleBarcode,
                'expected_qty' => $LotOrSingleQuantity,
                'field_extra1' => $Id,
                'extra_fields' => Json::encode(['apiLogValue' => $apiLogValue]),
            ];

//            file_put_contents('preparedReturnOrderPartyForSaveToDb.log','3'."\n",FILE_APPEND);
//            if($iForDebug == $iForDebugEnd) {
//                file_put_contents('preparedReturnOrderPartyForSaveToDb.log','4'."\n",FILE_APPEND);
//                break;
//            }
        }

        $outResult['HasError'] = false;
        $outResult['Data'] = $result;

        return $outResult;
    }

    /* Подготоваливаем данные полученые по апи от клиента с помощью GetWarehouseAppointments  и выводим  в грид.
    * @param array stdClass $data. Example values:
    * [Id] => 1514
    * [FromBusinessUnitId] => 835
    * [LcOrCartonLabel] => '2430000013394'
    * [NumberOfCartons] => '1.00'
    * [SkuId] => 2060081
    * [LotOrSingleBarcode] => '8681228230930'
    * [LotOrSingleQuantity] => '1.00'
    * [Status] => 'ReadyforProcessing'
    * [AppointmentBarcode] => 'D10AA00000090'
    * [ToBusinessUnitId] => 1029
    * [FlowType] => 'MixReturn'
    * @return array
    * */
    public function preparedReturnOrderPartyForShowInGrid($data)
    {
        $outResult = $this->_outResult;
        $result = [];
        $AppointmentBarcode = '';
        if (empty($data)) {
            $outResult['ErrorMessage'] = 'Нет данных для подготовки';
            return $outResult;
        }

        foreach ($data as $value) {

            $Id = ArrayHelper::getValue($value, 'Id', '');
            $FromBusinessUnitId = ArrayHelper::getValue($value, 'FromBusinessUnitId', '');
            $LcOrCartonLabel = ArrayHelper::getValue($value, 'LcOrCartonLabel', '');
            $NumberOfCartons = ArrayHelper::getValue($value, 'NumberOfCartons', '');
            $SkuId = ArrayHelper::getValue($value, 'SkuId', '');
            $LotOrSingleBarcode = ArrayHelper::getValue($value, 'LotOrSingleBarcode', '');
            $LotOrSingleQuantity = ArrayHelper::getValue($value, 'LotOrSingleQuantity', '');
            $Status = ArrayHelper::getValue($value, 'Status', '');
            $AppointmentBarcode = ArrayHelper::getValue($value, 'AppointmentBarcode', '');
            $ToBusinessUnitId = ArrayHelper::getValue($value, 'ToBusinessUnitId', '');
            $FlowType = ArrayHelper::getValue($value, 'FlowType', '');

            $apiLogValue = [
                'Id' => $Id,
                'FromBusinessUnitId' => $FromBusinessUnitId,
                'LcOrCartonLabel' => $LcOrCartonLabel,
                'NumberOfCartons' => $NumberOfCartons,
                'SkuId' => $SkuId,
                'LotOrSingleBarcode' => $LotOrSingleBarcode,
                'LotOrSingleQuantity' => $LotOrSingleQuantity,
                'Status' => $Status,
                'AppointmentBarcode' => $AppointmentBarcode,
                'ToBusinessUnitId' => $ToBusinessUnitId,
                'FlowType' => $FlowType,
            ];

            $result[] = [
                'client_id' => Client::CLIENT_DEFACTO,
                'order_number' => $AppointmentBarcode,
                'from_point_client_id' => $FromBusinessUnitId,
                'from_point_id' => $this->getStore($FromBusinessUnitId),
                'to_point_client_id' => $ToBusinessUnitId,
                'to_point_id' => $this->getStore($ToBusinessUnitId),
                'box_barcode_client' => $LcOrCartonLabel,
                'status_created_on_client' => $Status,
                'order_type' => ConsignmentUniversal::ORDER_TYPE_RETURN,
                'product_barcode' => $LotOrSingleBarcode,
                'expected_qty' => $LotOrSingleQuantity,
                'field_extra1' => $Id,
                'extra_fields' => Json::encode(['apiLogValue' => $apiLogValue]),
            ];
        }

        $outResult['HasError'] = false;
        $outResult['Data'] = $result;
        $outResult['AppointmentBarcode'] = $AppointmentBarcode;

        return $outResult;
    }

    /*
     * @param integer $id
     * */
    public function saveReturnOrderItemToOurDb($id)
    {
        $outResult = $this->_outResult;

        $consignmentUniversal = ConsignmentUniversal::findOne($id);
        if (!$consignmentUniversal) {
            $outResult['ErrorMessage'] = 'Накладная не найдена';
            return $outResult;
        }

        if (!ConsignmentUniversalOrdersItems::find()->andWhere(['consignment_universal_id' => $consignmentUniversal->id, 'status' => ConsignmentUniversal::STATUS_RETURN_NEW])->exists()) {
            $outResult['ErrorMessage'] = 'Нет новых возвратов';
            return $outResult;
        }

        $return = new ReturnOrder();
        $return->client_id = Client::CLIENT_DEFACTO;
        $return->party_number = $consignmentUniversal->party_number;
        $return->order_number = date("YmdHis");
        $return->status = ReturnOrder::STATUS_NEW;
        $return->save(false);
        $qty = 0;

        $consUniversalOrdersItems = ConsignmentUniversalOrdersItems::findAll(['consignment_universal_id' => $consignmentUniversal->id, 'status' => ConsignmentUniversal::STATUS_RETURN_NEW]);
        foreach ($consUniversalOrdersItems as $consUniversalOrdersItem) {

            $isExistReturnOrderItem = ReturnOrderItems::find()->andWhere([
                'product_barcode' => $consUniversalOrdersItem->product_barcode,
                'client_box_barcode' => $consUniversalOrdersItem->box_barcode_client,
                'field_extra1' => $consUniversalOrdersItem->field_extra1,
            ])->exists();

            if (!$isExistReturnOrderItem) {
                $returnItem = new ReturnOrderItems();
                $returnItem->return_order_id = $return->id;
                $returnItem->client_box_barcode = $consUniversalOrdersItem->box_barcode_client;
                $returnItem->product_barcode = $consUniversalOrdersItem->product_barcode;
                $returnItem->expected_qty = $consUniversalOrdersItem->expected_qty;
                $returnItem->product_serialize_data = $consUniversalOrdersItem->extra_fields;
                $returnItem->field_extra1 = $consUniversalOrdersItem->field_extra1;
                $returnItem->field_extra2 = $consUniversalOrdersItem->field_extra2;
                $returnItem->from_point_id = $consUniversalOrdersItem->from_point_id;
                $returnItem->from_point_client_id = $consUniversalOrdersItem->from_point_client_id;
                $returnItem->to_point_id = $consUniversalOrdersItem->to_point_id;
                $returnItem->to_point_client_id = $consUniversalOrdersItem->to_point_client_id;
                $returnItem->status = ReturnOrder::STATUS_NEW;
                $returnItem->save(false);

                $returnItemProduct = new ReturnOrderItemProduct();
                $returnItemProduct->return_order_id = $return->id;
                $returnItemProduct->return_order_item_id = $returnItem->id;
                $returnItemProduct->client_box_barcode = $consUniversalOrdersItem->box_barcode_client;
                $returnItemProduct->product_barcode = $consUniversalOrdersItem->product_barcode;
                $returnItemProduct->expected_qty = $consUniversalOrdersItem->expected_qty;
                $returnItemProduct->product_serialize_data = $consUniversalOrdersItem->extra_fields;
                $returnItemProduct->field_extra1 = $consUniversalOrdersItem->field_extra1;
                $returnItemProduct->field_extra2 = $consUniversalOrdersItem->field_extra2;
                $returnItemProduct->status = ReturnOrder::STATUS_NEW;
                $returnItemProduct->save(false);
                $qty++;
            }
        }

        $return->expected_qty = $qty;
        $return->save(false);

        ConsignmentUniversalOrdersItems::updateAll([
            'status' => ConsignmentUniversal::STATUS_RETURN_LOADED_SAVED,
            'order_number' => $return->order_number,
        ], ['consignment_universal_id' => $consignmentUniversal->id]);


        $outResult['HasError'] = false;
        $outResult['Message'] = "Данны успешно сохранены";
        return $outResult;
    }

    /*
     *
     * */
    public function SendInBoundFeedBackDataReturn($data) // OK
    {
        $outResult = $this->_outResult;
        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->SendInBoundFeedBackDataForReturn($data);
        if (!$dataFromAPI['HasError']) {
            $outResult['HasError'] = false;
            $outResult['Message'] = 'Данные успешно переданы дефакто';
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }
        return $outResult;
    }

    /*
     * @param integer $outboundOrder Outbound order model
     * @param integer $$boxCountStep Box number
     * @return string
     * */
    public static function makeWaybillNumber($outboundOrder, $boxCountStep)
    {
        $id = $outboundOrder->parent_order_number . $outboundOrder->order_number;

//        $isRuOrByStore = Store::find()->andWhere([
//            'shop_code3' => $outboundOrder->order_number,
//            'country_id' => [2, 3] // 2 - Беларусь 3 - Россия
//        ])->exists();
//
//        if ($isRuOrByStore) {
//            return $id;
//        }

        return $id . sprintf("%0" . (13 - strlen($id)) . "d", $boxCountStep);
    }

//
//    public function getColorByLotBarcode($lotBarcode,$colors = [])
//    {
//        $color = '';
//
//        if($color = Products::find()->select('Color')->andWhere(['LotOrSingleBarcode'=>$lotBarcode])->one()) {
//
//        }
//
//        $api = new DeFactoSoapAPIV2();
//        $dataFromAPI = $api->getMasterData(null,$lotBarcode);
//        if (!$dataFromAPI['HasError']) {
//            if (!empty($dataFromAPI['Data'])) {
//                $resultDataArray = $dataFromAPI['Data'];
//                $resultDataArray = count($resultDataArray) == 1 ? [$resultDataArray] : $resultDataArray;
//                foreach ($resultDataArray as $resultData) {
//
//                    if(isset($colors[$resultData->Color])) {
//                        $color = $colors[$resultData->Color];
//                        break;
//                    }
//
//                    $c2 = $resultData->Color[0].''.$resultData->Color[1];
//                    if(isset($colors[$c2])) {
//                        $color = $colors[$resultData->Color];
//                        break;
//                    }
//
//                    $c3 = $resultData->Color[0].''.$resultData->Color[1].''.$resultData->Color[2];
//                    if(isset($colors[$c3])) {
//                        $color = $colors[$c3];
//                        break;
//                    }
////                    file_put_contents('color-'.$fileName.'-'.date('Ymd').'.log', $resultData->Color . ";" . $stock['4'] . "\n", FILE_APPEND);
//                }
//            }
//            usleep(6000);
//        }
//
//        return $color;
//    }

    /*
    * Получаем весь список расходных накладных
    *
    * */
    /*    public function processingWarehousePickings()
        {
            $outResult = $this->_outResult;
            $api = new DeFactoSoapAPIV2();
            $dataFromAPI = $api->GetWarehousePickings();
            if(!$dataFromAPI['HasError']) {
                $preparedData =  $this->preparedWarehousePickingsForSaveToDb($dataFromAPI['Data']);
                if(!$preparedData['HasError']) {
    //                $saveToDbData = $this->saveWarehouseAppointmentsToDb($preparedData['Data']);
    //                if(!$saveToDbData['HasError']) {
                        $outResult['HasError'] = false;
                        $outResult['Data'] = $dataFromAPI['Data'];
    //                    $outResult['Message'] = $saveToDbData['Message'];
    //                    return $outResult;
    //                } else {
    //                    $outResult['ErrorMessage'] = $saveToDbData['ErrorMessage'];
    //                }
                } else {
                    $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
                }
            } else {
                $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
            }

            return $outResult;
        }*/

    /* Подготоваливаем данные полученые по апи от клиента с помощью GetWarehousePickings для сохранения в нашу базу
    * @param array stdClass $data. Example values:
    * [Id] => 16
    * [WarehouseId] => 1
    * [Status] => 'New'
    * [AsyncJobCount] => null
    * [ShiftDefinitionItemId] => null
    * [PickingType] => 'Shipment'
    * [PickingSubType] => 'Other'
    * [PickingFromLocationId] => 4
    * [PickingToLocationId] => 86571
    * [TotalQuantity] => '3.00'
    * [RemainingQuantity] => '0.00'
    * [BusinessUnitCount] => 1
    * [PickingSorterId] => 1
    * [PickingWMSStatus] => 'Nothing'
    * @return array
    * */
//    public function preparedWarehousePickingsForSaveToDb($data)
//    {
//        $outResult = $this->_outResult;
//        $result = [];
//        if(!empty($data)) {
//            foreach($data as $value) {
//                $id = ArrayHelper::getValue($value,'Id','');
//                $WarehouseId = ArrayHelper::getValue($value,'WarehouseId','');
//                $Status = ArrayHelper::getValue($value,'Status','');
//                $AsyncJobCount = ArrayHelper::getValue($value,'AsyncJobCount','');
//                $ShiftDefinitionItemId = ArrayHelper::getValue($value,'ShiftDefinitionItemId','');
//                $PickingType = ArrayHelper::getValue($value,'PickingType','');
//                $PickingSubType = ArrayHelper::getValue($value,'PickingSubType','');
//                $PickingFromLocationId = ArrayHelper::getValue($value,'PickingFromLocationId','');
//                $PickingToLocationId = ArrayHelper::getValue($value,'PickingToLocationId','');
//                $TotalQuantity = ArrayHelper::getValue($value,'TotalQuantity','');
//                $RemainingQuantity = ArrayHelper::getValue($value,'RemainingQuantity','');
//                $BusinessUnitCount = ArrayHelper::getValue($value,'BusinessUnitCount','');
//                $PickingSorterId = ArrayHelper::getValue($value,'PickingSorterId','');
//                $PickingWMSStatus = ArrayHelper::getValue($value,'PickingWMSStatus','');
//
//                $apiLogValue = [
//                    'id'=>$id,
//                    'WarehouseId'=>$WarehouseId,
//                    'Status'=>$Status,
//                    'AsyncJobCount'=>$AsyncJobCount,
//                    'ShiftDefinitionItemId'=>$ShiftDefinitionItemId,
//                    'PickingType'=>$PickingType,
//                    'PickingSubType'=>$PickingSubType,
//                    'PickingFromLocationId'=>$PickingFromLocationId,
//                    'PickingToLocationId'=>$PickingToLocationId,
//                    'TotalQuantity'=>$TotalQuantity,
//                    'RemainingQuantity'=>$RemainingQuantity,
//                    'BusinessUnitCount'=>$BusinessUnitCount,
//                    'PickingSorterId'=>$PickingSorterId,
//                    'PickingWMSStatus'=>$PickingWMSStatus,
//                ];
//
//                $result[] = [
//                    'client_id'=>Client::CLIENT_DEFACTO,
////                    'party_number'=>$AppointmentBarcode,
////                    'status_created_on_client'=>$WarehouseAppointmentWMSStatus,
//                    'extra_fields'=>Json::encode(['apiLogValue'=>$apiLogValue]),
//                ];
//            }
//
//            $outResult['HasError'] = false;
//            $outResult['Data'] = $result;
//
//        } else {
//            $outResult['ErrorMessage'] = 'Нет данных для подготовки';
//        }
//
//        return $outResult;
//    }

    /*
    * Send "Mark Picking for Out Bound" status
    * @param integer $pickingID
    * @return array
    * */
    /*    public function saveMarkPickingForOutBound($pickingID)
        {
            $outResult = $this->_outResult;
            $api = new DeFactoSoapAPIV2();
            $dataFromAPI = $api->MarkPickingforOutBound($pickingID);
            if(!$dataFromAPI['HasError']) {
                $outResult['HasError'] = false;
                $outResult['Message'] = 'Данные успешно переданы дефакто';
            } else {
                $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
            }
            return $outResult;
        }*/

    /*
    * Получаем весь список приходных накладных
     * @param integer $pickingID
     * @return array
    * */
    /*    public function processingPickingOutBoundData($pickingID)
        {
            $outResult = $this->_outResult;
            $api = new DeFactoSoapAPIV2();
            $dataFromAPI = $api->GetPickingOutBoundData($pickingID);
            if(!$dataFromAPI['HasError']) {
    //            $preparedData =  $this->preparedWarehousePickingsForSaveToDb($dataFromAPI['Data']);
    //            if(!$preparedData['HasError']) {
    //                $saveToDbData = $this->saveWarehouseAppointmentsToDb($preparedData['Data']);
    //                if(!$saveToDbData['HasError']) {
                    $outResult['HasError'] = false;
                    $outResult['Data'] = $dataFromAPI['Data'];
    //                    $outResult['Message'] = $saveToDbData['Message'];
    //                    return $outResult;
    //                } else {
    //                    $outResult['ErrorMessage'] = $saveToDbData['ErrorMessage'];
    //                }
    //            } else {
    //                $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
    //            }
            } else {
                $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
            }

            return $outResult;
        }*/

    /*
    * Обновляем мастер дату данными из апи дефакто
    * @param string $type Value 'Full' or 'Changed'
    * @return boolean
    * */
//    public function getUpdateMasterData($type='Changed')
//    {
//        $outResult = $this->_outResult;
//        $api = new DeFactoSoapAPIV2();
//        $dataFromAPI = $api->getMasterData($type);
//        if(!$dataFromAPI['HasError']) {
//
//        }
//    }

    /* Подготоваливаем данные полученые по апи от клиента с помощью getMasterData для сохранения в нашу базу
    * @param array stdClass $data. Example values:
    * @return array
    * */
//    private function preparedMasterDataForSaveToDb($data)
//    {
//        $outResult = $this->_outResult;
//        $result = [];
//        if(!empty($data)) {
//            foreach($data as $value) {
//                $id = ArrayHelper::getValue($value,'Id','');
//                $WarehouseId = ArrayHelper::getValue($value,'WarehouseId','');
//                $AppointmentUniversalIdentifierId = ArrayHelper::getValue($value,'AppointmentUniversalIdentifierId','');
//                $AppointmentDate = ArrayHelper::getValue($value,'AppointmentDate','');
//                $Active = ArrayHelper::getValue($value,'Active','');
//                $BusinessUnitId = ArrayHelper::getValue($value,'BusinessUnitId','');
//                $AppointmentBarcode = ArrayHelper::getValue($value,'AppointmentBarcode','');
//                $WarehouseAppointmentWMSStatus = ArrayHelper::getValue($value,'WarehouseAppointmentWMSStatus','');
//
//                $apiLogValue = [
//                    'id'=>$id,
//                    'WarehouseId'=>$WarehouseId,
//                    'AppointmentUniversalIdentifierId'=>$AppointmentUniversalIdentifierId,
//                    'AppointmentDate'=>$AppointmentDate,
//                    'Active'=>$Active,
//                    'BusinessUnitId'=>$BusinessUnitId,
//                    'AppointmentBarcode'=>$AppointmentBarcode,
//                    'WarehouseAppointmentWMSStatus'=>$WarehouseAppointmentWMSStatus,
//                ];
//
//                $result[] = [
//                    'client_id'=>Client::CLIENT_DEFACTO,
//                    'party_number'=>$AppointmentBarcode,
//                    'status_created_on_client'=>$WarehouseAppointmentWMSStatus,
//                    'extra_fields'=>Json::encode(['apiLogValue'=>$apiLogValue]),
//                ];
//            }
//
//            $outResult['HasError'] = false;
//            $outResult['Data'] = $result;
//
//        } else {
//            $outResult['ErrorMessage'] = 'Нет данных для подготовки';
//        }
//
//        return $outResult;
//    }

//    private function saveUpdateMasterDataToDb($data)
//    {
//        $outResult = $this->_outResult;
//        if(!empty($data)) {
//            if($cu = ConsignmentUniversal::findOne(['id'=>$id,'status_created_on_client'=>DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED])) {
//                if (!($in = InboundOrder::findOne(['party_number' =>$cu->party_number, 'client_id' =>$cu->client_id]))) {
//                    $in = new InboundOrder();
//                }
//                $in->client_id = $cu->client_id;
//                $in->status = Stock::STATUS_INBOUND_NEW;
//                $in->order_number = $cu->party_number;
//                $in->order_type = InboundOrder::ORDER_TYPE_INBOUND;
//                $in->expected_qty = 0;
//                $in->accepted_qty = 0;
//                $in->save(false);
//
//                foreach ($data as $value) {
//                    $inItem = new InboundOrderItem();
//                    $inItem->setAttributes($value, false);
//                    $inItem->save(false);
//                }
//                $outResult['HasError'] = false;
//                $outResult['Message'] = 'Данные в базу успешно сохранены';
//            } else {
//                $outResult['ErrorMessage'] = 'Накладная с id '.$id.'  и статусом '.DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED.' не найдена. возможно данные еще не подготовлены. Попробуйте снова через 5 мин';
//            }
//        } else {
//            $outResult['ErrorMessage'] = 'Нет данных для сохранения в базу';
//        }
//        return $outResult;
//    }


    /*
 * Save to db "Mark Appointment for In Bound" status
* @param string $appointmentBarcode
* */
//    public function saveMarkAppointmentForInBoundToDb($appointmentBarcode)
//    {
//        $outResult = $this->_outResult;
//        if(!empty($appointmentBarcode)) {
//            if($cu = ConsignmentUniversal::find()->andWhere(['party_number'=>$appointmentBarcode,'client_id'=>Client::CLIENT_DEFACTO])->one()) {
//                $cu->status = ConsignmentUniversal::STATUS_MARK_APPOINTMENT_FOR_IN_BOUND_CLIENT_DEFACTO;
//                $cu->save(false);
//                $outResult['HasError'] = false;
//                $outResult['Message'] = 'Данные в базу успешно сохранены';
//            } else {
//                $outResult['ErrorMessage'] = 'Приходная накладная под номером '.$appointmentBarcode.' не найдена';
//            }
//        } else {
//            $outResult['ErrorMessage'] = 'Необходимо указать appointmentBarcode. Номер приходной накладной ';
//        }
//        return $outResult;
//    }
}