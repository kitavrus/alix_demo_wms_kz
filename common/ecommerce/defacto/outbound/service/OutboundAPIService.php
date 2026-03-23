<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 26.08.2019
 * Time: 9:41
 */

namespace common\ecommerce\defacto\outbound\service;


use common\ecommerce\constants\OutboundPackageType;
use common\ecommerce\defacto\api\service\EcommerceAPILogService;
use common\ecommerce\entities\EcommerceApiOutboundLog;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

use DateTime;
use DateTimeZone;
use common\ecommerce\defacto\outbound\dto\SendCargoDeliveryIn;

class OutboundAPIService
{
    private $api;

    /*
 * @var array Default format function result
 * */
    private $_outResult = [
        'HasError' => true,
        'ErrorMessage' => '',
        'Message' => '',
        'Data' => [],
    ];

    /**
     * InboundAPI constructor.
     */
    public function __construct()
    {
        //$this->api = new \common\ecommerce\defacto\api\ECommerceAPI();
        $this->api = new \common\ecommerce\defacto\api\ECommerceAPINew();
    }

    private function makeGetShipmentsRequest($OrderQuantity = 30)
    {
        $params = [];
        $params['request'] = [
            'BusinessUnitId' => $this->api->BUSINESS_UNIT_ID(),
            'OrderQuantity' => $OrderQuantity,
        ];

        return $params;
    }

    private function parseGetShipments($aApiResponse)
    {
        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'GetShipmentsResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if ($data = @ArrayHelper::getValue($result, 'ResultList.B2CShipmentDto')) {
                    $resultDataArray = count($data) <= 1 ? [$data] : $data;
                    $outResult['HasError'] = false;
                    $outResult['Data'] = $resultDataArray;

                    return $outResult;
                } else {
                    $outResult['ErrorMessage'] = 'Дефакто вернул пустоту';
                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.[' . ArrayHelper::getValue($result, 'Error') . ']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    public function GetShipments($OrderQuantity = 30)
    {
        $request = $this->makeGetShipmentsRequest($OrderQuantity);
//        $requestInDB = GetShipmentsRequestService::save($request);
        $APILogService = new EcommerceAPILogService();
        $APILogService->GetShipmentsRequest($request);

        $shipmentList = $this->api->GetShipments($request);
//        $shipmentList = $this->testGetShipments();
        $parsedShipmentList = $this->parseGetShipments($shipmentList);
//        VarDumper::dump($shipmentList,10,true);
//        VarDumper::dump($parsedShipmentList,10,true);
//        die;

//        if($parsedShipmentList['HasError'] == false) {
//            $getShipmentsResponseService = new GetShipmentsResponseService();
//            return $getShipmentsResponseService->save($parsedShipmentList,$requestInDB);
//        }

        $APILogService->GetShipmentsResponse($parsedShipmentList);

//        $x = EcommerceApiOutboundLog::find()->andWhere(['id'=>28])->one();
//        $parsedShipmentList = unserialize($x->response_data);

        return $parsedShipmentList;
    }
	

	public function GetShipmentsV2($OrderQuantity = 30)
	{
		$request = $this->makeGetShipmentsRequest($OrderQuantity);
//        $requestInDB = GetShipmentsRequestService::save($request);
		$APILogService = new EcommerceAPILogService();
		$APILogService->GetShipmentsRequest($request);

		$shipmentList = $this->api->GetShipmentsV2($request);
//        $shipmentList = $this->testGetShipments();
		$parsedShipmentList = $this->parseGetShipments($shipmentList);
//        VarDumper::dump($shipmentList,10,true);
//        VarDumper::dump($parsedShipmentList,10,true);
//        die;

//        if($parsedShipmentList['HasError'] == false) {
//            $getShipmentsResponseService = new GetShipmentsResponseService();
//            return $getShipmentsResponseService->save($parsedShipmentList,$requestInDB);
//        }

		$APILogService->GetShipmentsResponse($parsedShipmentList);

//        $x = EcommerceApiOutboundLog::find()->andWhere(['id'=>28])->one();
//        $parsedShipmentList = unserialize($x->response_data);

		return $parsedShipmentList;
	}

    private function testGetShipments() {
        $shipmentList = [];
        $shipmentList['errors'] = [];
        $shipmentList['response'] = new \stdClass();
        $shipmentList['response']->GetShipmentsResult = new \stdClass();
        $shipmentList['response']->GetShipmentsResult->HasError = false;
        $shipmentList['response']->GetShipmentsResult->IsSuccess = 1;
        $shipmentList['response']->GetShipmentsResult->ResultList = new \stdClass();
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto = new \stdClass();

        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ExternalShipmentId = 'OMC-8176705';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShipmentType = 'STD';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShipmentSource = 'ECP-KZ';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShipmentDate = new \stdClass();
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->Priority = 3;
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->CustomerName = 'Анастасия  Шигапова';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShippingAddress = 'Валиханова 244';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShippingCountryCode = 'KZ';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShippingCity = 'Shymkent';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShippingCounty = '';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShippingZipCode = 160000;
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShippingEmail = 'nika__000@mail.ru';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->ShippingPhone = '87083997587';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->Destination = 'INTERNATIONAL';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->CourierCompany = 'Pony';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->FromBusinessUnitId = 95540;
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->CacStoreID = 0;
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->StoreName = 'KZK B2C DC';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->PartyApprovalId = 0;
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->IsGiftWrapping = '';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList = new \stdClass();
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList->B2CShipmentDetailDto = new \stdClass();
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList->B2CShipmentDetailDto->SkuId = 223508891;
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList->B2CShipmentDetailDto->Quantity = 0;
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList->B2CShipmentDetailDto->ItemMessage = 'Ürün barkodunu kesiniz!';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList->B2CShipmentDetailDto->ProductCode = 'I3784AZ_RD44_3XL';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList->B2CShipmentDetailDto->ProductName = 'Bisiklet Yaka Triko Kazak';
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList->B2CShipmentDetailDto->UnitPrice = 1990.00;
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList->B2CShipmentDetailDto->UnitTax = 127.93;
        $shipmentList['response']->GetShipmentsResult->ResultList->B2CShipmentDto->B2CShipmentDetailList->B2CShipmentDetailDto->UnitDiscount = 796.00;

        return $shipmentList;
    }


    private function makeGetCargoLabelRequest($orderInfo)
    {
        $items = [];
        $items['Item'] = [];

        foreach ($orderInfo->items as $productInfo) {

            if($productInfo->accepted_qty < 1) {
                continue;
            }

			$qrCode = [];
			foreach ($orderInfo->stocks as $stock) {
				if ($stock["product_barcode"] == $productInfo->product_barcode && !empty($stock["product_qrcode"])) {
					$qrCode[] = substr($stock["product_qrcode"], 0, 31);
				}
			}

			$QRCodeList = [];
			if (!empty($qrCode)) {
				$QRCodeList = $qrCode;
			}

            $items['Item'][] = [
                'SkuId'=> $productInfo->product_sku,
                'Quantity'=> $productInfo->accepted_qty,
				"QRCodeList" => $QRCodeList,
            ];
        }

        $request = [];
        $request['request'] = [
            'ExternalShipmentId' => $orderInfo->order->order_number,
            'VolumetricWeight' =>  $orderInfo->order->kg,
            'CargoCompany' =>  $orderInfo->order->client_CargoCompany,
            'PackageId' => $orderInfo->order->package_type,//OutboundPackageType::KOLI1, // потом поменять
            'ShipmentItemInfos' => $items,
        ];

        return $request;
    }

    private function parseGetCargoLabel($aApiResponse)
    {
        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'GetCargoLabelResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
                $outResult['Data'] = $result;

                return $outResult;
//                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.[' . ArrayHelper::getValue($result, 'Error') . ']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }


    public function GetCargoLabel($orderInfo)
    {
        $APILogService = new EcommerceAPILogService();
        $request = $this->makeGetCargoLabelRequest($orderInfo);
        $APILogService->GetCargoLabelRequest($orderInfo->order->id,$request);
        $responseByAPI = $this->api->GetCargoLabel($request);
        $preparedResponseByAPI = $this->parseGetCargoLabel($responseByAPI);
        $APILogService->CancelShipmentResponse($preparedResponseByAPI);
        return $preparedResponseByAPI;
    }

    private function makeSendShipmentFeedbackRequest($orderInfo)
    {
        $items = [];
        foreach ($orderInfo->items as $item) {

            if($item->accepted_qty < 1) {
                continue;
            }

			$qrCode = [];
			foreach ($orderInfo->stocks as $stock) {
				if ($stock["product_barcode"] == $item->product_barcode && !empty($stock["product_qrcode"])) {
					$qrCode[] = substr($stock["product_qrcode"], 0, 31);
				}
			}

			$QRCodeList = [];
			if (!empty($qrCode)) {
				$QRCodeList = $qrCode;
			}


            $items[] = [
                'BusinessUnitId' => $this->api->BUSINESS_UNIT_ID(),
                'ExternalShipmentId' =>  $orderInfo->order->order_number,
                'SkuId' => $item->product_sku,
                'SkuBarcode' => $item->product_barcode,
                'Quantity' => $item->accepted_qty,
                'WaybillSerial' => 'KZKECOM',
                'WaybillNumber' =>  $orderInfo->order->id,
				"QRCodeList" => $QRCodeList,
            ];
        }

        $request = [];
        $request['request']['B2CShipmentFeedBackList']['B2CShipmentFeedBackDto'] = $items;

        return $request;
    }

    private function parseSendShipmentFeedback($aApiResponse)
    {

        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'SendShipmentFeedbackResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
                $outResult['Data'] = $result;

                return $outResult;
//                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.[' . ArrayHelper::getValue($result, 'Error') . ']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }


    public function SendShipmentFeedback($orderInfo)
    {
        $APILogService = new EcommerceAPILogService();
        $requestByAPI = $this->makeSendShipmentFeedbackRequest($orderInfo);
        $APILogService->SendShipmentFeedbackRequest($orderInfo->order->id,$requestByAPI);
        $responseByAPI = $this->api->SendShipmentFeedback($requestByAPI);
        $preparedResponseByAPI = $this->parseSendShipmentFeedback($responseByAPI);
        $APILogService->SendShipmentFeedbackResponse($preparedResponseByAPI);
        return $preparedResponseByAPI;
    }


    public function CancellationRequestExistsByCustomer($ourOutboundId, $orderNumber)
    {
        $APILogService = new EcommerceAPILogService();

        $requestByAPI = $this->makeCancellationRequestExistsByCustomerRequest($orderNumber);
        $APILogService->CancellationRequestExistsByCustomerRequest($ourOutboundId, $requestByAPI);
        $responseByAPI = $this->api->CancellationRequestExistsByCustomer($requestByAPI);
        $preparedResponseByAPI = $this->parseCancellationRequestExistsByCustomer($responseByAPI);

        $APILogService->CancellationRequestExistsByCustomerResponse($preparedResponseByAPI);

        return $preparedResponseByAPI;
    }

    private function makeCancellationRequestExistsByCustomerRequest($externalShipmentId)
    {
        $request = [];
        $request['request'] = [
            'BusinessUnitId' => $this->api->BUSINESS_UNIT_ID(),
            'ExternalShipmentId' => $externalShipmentId,
        ];

        return $request;
    }

    private function parseCancellationRequestExistsByCustomer($aApiResponse)
    {

        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'CancellationRequestExistsByCustomerResult')) {
//            if (ArrayHelper::getValue($result, 'Canceled') === false) {
            $outResult['HasError'] = false;
            $outResult['Data'] = ArrayHelper::getValue($result, 'Canceled');
            return $outResult;
//                }
//            } else {
//                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
//            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    public function SendAcceptedShipments($ourOutboundId, $orderNumber)
    {
        $APILogService = new EcommerceAPILogService();

        $requestByAPI = $this->makeSendAcceptedShipmentsRequest($orderNumber);
        $APILogService->SendAcceptedShipmentsRequest($ourOutboundId, $requestByAPI);
        $responseByAPI = $this->api->SendAcceptedShipments($requestByAPI);
        $preparedResponseByAPI = $this->parseSendAcceptedShipments($responseByAPI);

        $APILogService->SendAcceptedShipmentsResponse($requestByAPI);

        return $preparedResponseByAPI;
    }

    private function makeSendAcceptedShipmentsRequest($externalShipmentId)
    {
        $ExternalShipmentIdList = [];
        $externalShipmentId =  !is_array($externalShipmentId)  ? [$externalShipmentId] : $externalShipmentId;
        $request = [];
        $request['request'] = [
            'BusinessUnitId' => $this->api->BUSINESS_UNIT_ID(),
            'ExternalShipmentIdList' => ArrayHelper::merge($ExternalShipmentIdList, $externalShipmentId)
        ];

        return $request;
    }

    private function parseSendAcceptedShipments($aApiResponse)
    {
        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'SendAcceptedShipmentsResult')) {
//            if (ArrayHelper::getValue($result, 'Canceled') === false) {
            $outResult['HasError'] = false;
            $outResult['Data'] = ArrayHelper::getValue($result, 'IsSuccess');
            return $outResult;
//                }
//            } else {
//                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
//            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    public function CancelShipment($orderInfo)
    {
        $APILogService = new EcommerceAPILogService();

        $request = $this->makeCancelShipmentRequest($orderInfo);
        $APILogService->CancelShipmentRequest($orderInfo->order->id,$request);
        $responseByAPI = $this->api->CancelShipment($request);
        $preparedResponseByAPI = $this->parseCancelShipment($responseByAPI);
        $APILogService->CancelShipmentResponse($preparedResponseByAPI);
        return $preparedResponseByAPI;
    }

    private function makeCancelShipmentRequest($orderInfo)
    {
        $items = [];
        $items['Item'] = [];
        foreach ($orderInfo->items as $productInfo) {
            $items['Item'][] = [
                'SkuId' => $productInfo->product_sku,
                'Quantity' => $productInfo->expected_qty,
            ];
        }

        $request = [];
        $request['request'] = [
            'BusinessUnitId' =>$this->api->BUSINESS_UNIT_ID(),
            'ExternalShipmentId' => $orderInfo->order->order_number,
            'Reason' =>  $orderInfo->order->client_CancelReason,// 'UnableToFulfil',
            'CancellationRowId' => $orderInfo->order->id,
            'ShipmentItemInfos' => $items,
        ];

        return $request;
    }


    private function parseCancelShipment($aApiResponse)
    {
        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'CancelShipmentResult')) {
                $outResult['HasError'] = false;
                $outResult['Data'] = ArrayHelper::getValue($result, 'IsSuccess');
                return $outResult;
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }


    public function PartialCancelShipment($orderInfo)
    {
        $APILogService = new EcommerceAPILogService();

        $request = $this->makePartialCancelShipmentRequest($orderInfo);
        $APILogService->CancelShipmentRequest($orderInfo->order->id,$request);
        $responseByAPI = $this->api->CancelShipment($request);
        $preparedResponseByAPI = $this->parsePartialCancelShipment($responseByAPI);
        $APILogService->CancelShipmentResponse($preparedResponseByAPI);
        return $preparedResponseByAPI;
    }

    private function makePartialCancelShipmentRequest($orderInfo)
    {
        $items = [];
        $items['Item'] = [];
        foreach ($orderInfo->items as $productInfo) {
            $Quantity = $productInfo->expected_qty - $productInfo->accepted_qty;

            if($Quantity < 1) {
                continue;
            }

            $items['Item'][] = [
                'SkuId' => $productInfo->product_sku,
                'Quantity' => $Quantity,
            ];
        }

        $request = [];
        $request['request'] = [
            'BusinessUnitId' =>$this->api->BUSINESS_UNIT_ID(),
            'ExternalShipmentId' => $orderInfo->order->order_number,
            'Reason' =>  $orderInfo->order->client_CancelReason,// 'UnableToFulfil',
            'CancellationRowId' => $orderInfo->order->id,
            'ShipmentItemInfos' => $items,
        ];

        return $request;
    }


    private function parsePartialCancelShipment($aApiResponse)
    {
        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'CancelShipmentResult')) {
            $outResult['HasError'] = false;
            $outResult['Data'] = ArrayHelper::getValue($result, 'IsSuccess');
            return $outResult;
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }


    /**
     * @param string $LotOrSingleBarcode
     * @param int $Quantity
     * @param string $Operator Возможные значения "+"  или "-"
     * @return array
     */
    public function StockAdjustment($LotOrSingleBarcode,$Quantity,$Operator)
    {
        $APILogService = new EcommerceAPILogService();

        $request = $this->makeStockAdjustmentRequest($LotOrSingleBarcode,$Quantity,$Operator);
        $APILogService->StockAdjustmentRequest($request);
        $responseByAPI = $this->api->StockAdjustment($request);
        $preparedResponseByAPI = $this->parseStockAdjustment($responseByAPI);
        $APILogService->StockAdjustmentResponse($preparedResponseByAPI);
        return $preparedResponseByAPI;
    }

    private function makeStockAdjustmentRequest($LotOrSingleBarcode,$Quantity,$Operator)
    {
        $request = [];
        $request['request'] = [
            'BusinessUnitId' =>$this->api->BUSINESS_UNIT_ID(),
            'LotOrSingleBarcode' => $LotOrSingleBarcode,
            'Quantity' => $Quantity,
            'Operator' => $Operator,
        ];

        return $request;
    }


    private function parseStockAdjustment($aApiResponse)
    {
        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'StockAdjustmentResult')) {
            $outResult['HasError'] = false;
            $outResult['Data'] = ArrayHelper::getValue($result, 'IsSuccess');
            return $outResult;
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    /**
     * @param string $ExternalShipmentId
     * @param string $FileExtension
     * @param string $FileBase64
     * @param string $FileType
     * @return array
     */
    public function UploadShipmentFile($ExternalShipmentId,$FileBase64,$FileExtension = 'pdf',$FileType = 'Waybill')
    {
        $APILogService = new EcommerceAPILogService();

        $request = $this->makeUploadShipmentFileRequest($ExternalShipmentId,$FileBase64,$FileExtension,$FileType);
        $APILogService->StockAdjustmentRequest($request);
        $responseByAPI = $this->api->StockAdjustment($request);
        $preparedResponseByAPI = $this->parseUploadShipmentFile($responseByAPI);
        $APILogService->StockAdjustmentResponse($preparedResponseByAPI);
        return $preparedResponseByAPI;
    }

    private function makeUploadShipmentFileRequest($ExternalShipmentId,$FileBase64,$FileExtension,$FileType)
    {
        $request = [];
        $request['request'] = [
            'ExternalShipmentId' => $ExternalShipmentId,
            'FileExtension' => $FileExtension,
            'FileBase64' => $FileBase64,
            'FileType' => $FileType,
        ];
        return $request;
    }


    private function parseUploadShipmentFile($aApiResponse)
    {
        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'UploadShipmentFileResult')) {
                $outResult['HasError'] = false;
                $outResult['Data'] = ArrayHelper::getValue($result, 'IsSuccess');
                return $outResult;
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }
	/// ---------------------------------------------------------------------
	
	
	/**
	 * @param SendCargoDeliveryIn $sendCargoDeliveryIn
	 * BusinessUnitId: B2C DC Business Unit Id
	 * ExternalShipmentId: The package for shipment which will be delivered to the Cargo
	 * CourierCompany : The company which delivering the shipments
	 * DeliveryDate: Delivery date of box. Format for date is YYYY-MM-DD HH:MM:SS zzz. E.g. : 2019-03-02 13:42:05 +03:00 (year-month-day hour:minute:second Time Zone)
	 * CargoShipmentNo: Tracking no of box given by cargo company
	 * VolumetricWeight: Volumetric weight of box in deci. E.g. :32, 64
	 * TrackingUrl: Orders delivery steps can follow that link
	 * @throws \Exception
	 */
	private function makeSendCargoDeliveryRequest($sendCargoDeliveryIn)
	{
		$tz = new DateTimeZone('Asia/Almaty');
		$ts = new DateTime('now',$tz);
		$request = [];
		$request['request'] = [
			'BusinessUnitId' => $this->api->BUSINESS_UNIT_ID(),
			'ExternalShipmentId' => $sendCargoDeliveryIn->orderNumber,
			'CourierCompany' => $sendCargoDeliveryIn->courierCompany,
			'DeliveryDate' => $ts->format('Y-m-d H:i:s O'),
			'CargoShipmentNo' =>  $sendCargoDeliveryIn->cargoShipmentNo,
			'VolumetricWeight' =>  $sendCargoDeliveryIn->kg,
			'TrackingUrl' =>  $sendCargoDeliveryIn->trackingUrl,
		];

		return $request;
	}

	/**
	 * @param SendCargoDeliveryIn $sendCargoDeliveryIn
	 * @throws \Exception
	 */
    public function SendCargoDelivery($sendCargoDeliveryIn)
    {
        $APILogService = new EcommerceAPILogService();
        $request = $this->makeSendCargoDeliveryRequest($sendCargoDeliveryIn);
        $APILogService->SendCargoDeliveryRequest($sendCargoDeliveryIn->orderId,$request);
        $responseByAPI = $this->api->SendCargoDelivery($request);
        $preparedResponseByAPI = $this->parseSendCargoDelivery($responseByAPI);
        $APILogService->SendCargoDeliveryResponse($preparedResponseByAPI);
        return $preparedResponseByAPI;
    }

	private function parseSendCargoDelivery($aApiResponse)
	{
		$outResult = $this->_outResult;
		if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'SendCargoDeliveryResult')) {
			if (ArrayHelper::getValue($result, 'HasError') === false) {
				$outResult['HasError'] = false;
				$outResult['Data'] = $result;

				return $outResult;
//                }
			} else {
				$outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.[' . ArrayHelper::getValue($result, 'Error') . ']';
			}
		} else {
			$outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
		}

		return $outResult;
	}




	public function PartialCancellationRequestExistsByCustomer($ourOutboundId, $orderNumber)
	{
		$APILogService = new EcommerceAPILogService();

		$requestByAPI = $this->makePartialCancellationRequestExistsByCustomerRequest($orderNumber);
		$APILogService->CancellationRequestExistsByCustomerRequest($ourOutboundId, $requestByAPI);
		$responseByAPI = $this->api->PartialCancellationRequestExistsByCustomer($requestByAPI);
		$preparedResponseByAPI = $this->parsePartialCancellationRequestExistsByCustomer($responseByAPI);
		$APILogService->CancellationRequestExistsByCustomerResponse($preparedResponseByAPI);

		//return $responseByAPI;
		 return $preparedResponseByAPI;
	}

	private function makePartialCancellationRequestExistsByCustomerRequest($externalShipmentId)
	{
		$request = [];
		$request['request'] = [
			'BusinessUnitId' => $this->api->BUSINESS_UNIT_ID(),
			'ExternalShipmentIdList' => [$externalShipmentId],
		];

		return $request;
	}

	private function parsePartialCancellationRequestExistsByCustomer($aApiResponse)
	{
		$outResult = $this->_outResult;
		if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'PartialCancellationRequestExistsByCustomerResult')) {
			$outResult['HasError'] = false;
			$outResult['Data'] = [
				@ArrayHelper::getValue($result, 'Data.B2CCheckPartialCancellationResponse')
			];
			return $outResult;
		} else {
			$outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
		}
		return $outResult;
	}
}