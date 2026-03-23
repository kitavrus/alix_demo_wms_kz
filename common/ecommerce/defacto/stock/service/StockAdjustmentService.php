<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 28.03.2020
 * Time: 10:32
 */

namespace common\ecommerce\defacto\stock\service;


use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\constants\StockInboundStatus;
use common\ecommerce\defacto\api\service\EcommerceAPILogService;
use yii\helpers\ArrayHelper;

class StockAdjustmentService
{
    private $api;
    private $stockService;

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
        $this->api = new \common\ecommerce\defacto\api\ECommerceAPI();
        $this->stockService = new Service();
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
            $outResult['Data'] = ArrayHelper::getValue($result, 'HasError');
            return $outResult;
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    public function change($aDto) {

        $stockAdjustment = new \common\ecommerce\entities\EcommerceStockAdjustment();
        $stockAdjustment->product_barcode = $aDto->productBarcode;
        $stockAdjustment->product_quantity = $aDto->productQuantity;
        $stockAdjustment->product_operator = $aDto->productOperator;
        $stockAdjustment->reason = $aDto->reason;
        $stockAdjustment->save(false);

        switch($aDto->productOperator) {
            case '+':
                $this->ifOperatorPlus($stockAdjustment);
                $result = 'Y';
                break;
            case '-':
                $this->ifOperatorMinus($stockAdjustment);
                $result = 'Y';
                break;
            default:
                $result = 'N';
        }

        if($result == 'Y') {
            $this->StockAdjustment($aDto->productBarcode,$aDto->productQuantity,$aDto->productOperator);
        }

        return $result;
    }

    private function ifOperatorPlus($aStockAdjustment) {

        for ($i = 1; $i <=  $aStockAdjustment->product_quantity; $i++) {
            // создает DTO для стока
            $dtoForCreateStock = new \stdClass();
            $dtoForCreateStock->clientId = 2;
            $dtoForCreateStock->inboundId = null;
            $dtoForCreateStock->productBarcode = $aStockAdjustment->product_barcode;
            $dtoForCreateStock->conditionType = StockConditionType::UNDAMAGED;
            $dtoForCreateStock->clientBoxBarcode = '';
            $dtoForCreateStock->boxAddressBarcode = '19999999999';
            $dtoForCreateStock->placeAddressBarcode = '4-99-99-99';
            $dtoForCreateStock->statusInbound = StockInboundStatus::_NEW;
            $dtoForCreateStock->statusAvailability = StockAvailability::YES;
            $dtoForCreateStock->scanInDatetime = $this->stockService->makeScanInboundDatetime();
            $dtoForCreateStock->stockAdjustmentId = $aStockAdjustment->id;
            $dtoForCreateStock->stockAdjustmentStatus = 1;
            $dtoForCreateStock->apiStatus = StockAPIStatus::YES;

            $this->stockService->create($dtoForCreateStock);
        }
    }

    private function ifOperatorMinus($aStockAdjustment) {
//        $stock = EcommerceStock::find()
//            ->andWhere('outbound_id != 0')
//            ->andWhere(['product_barcode'=>$this->productBarcode])
//            ->andWhere(['status_outbound'=>StockOutboundStatus::PRINTED_PICKING_LIST])
//            ->andWhere('scan_out_employee_id IS NULL')
//            ->andWhere('reason_re_reserved != "" ')
//            ->asArray()
//            ->all();
//        VarDumper::dump($stock,10,true);
//        die;
    }
}