<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 28.03.2020
 * Time: 10:32
 */

namespace common\b2b\domains\stock\service;

use common\modules\stock\service\Service;
use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
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
        $this->api = new DeFactoSoapAPIV2();
        $this->stockService = new Service();
    }

    public function change($dto,$stockId = null) {

        $stockAdjustment = new \common\b2b\domains\stock\entities\StockAdjustment();
        $stockAdjustment->product_barcode = $dto->productBarcode;
        $stockAdjustment->product_quantity = $dto->productQuantity;
        $stockAdjustment->product_operator = $dto->productOperator;
        $stockAdjustment->reason = $dto->reason;
        $stockAdjustment->address_box_barcode = $dto->addressBoxBarcode;
        $stockAdjustment->save(false);

        switch($dto->productOperator) {
            case '+':
                $this->ifOperatorPlus($stockAdjustment);
                $result = 'Y';
                break;
            case '-':
                $this->ifOperatorMinus($stockAdjustment,$stockId);
                $result = 'Y';
                break;
            default:
                $result = 'N';
        }

        if($result == 'Y') {
            $this->StockAdjustment($this->productBarcode,$this->productQuantity,$this->productOperator);
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
            $dtoForCreateStock->conditionType = Stock::CONDITION_TYPE_NOT_SET;
            $dtoForCreateStock->primaryAddress = $aStockAdjustment->address_box_barcode ;//'19999999999';
            $dtoForCreateStock->secondaryAddress = '1-99-99-99';
            $dtoForCreateStock->statusAvailability = Stock::STATUS_AVAILABILITY_YES;
            $dtoForCreateStock->scanInDatetime = $this->stockService->makeScanInboundDatetime();
            $dtoForCreateStock->stockAdjustmentId = $aStockAdjustment->id;
            $dtoForCreateStock->stockAdjustmentStatus = 1;
            $dtoForCreateStock->status = Stock::STATUS_NOT_SET;

            $this->stockService->create($dtoForCreateStock);
        }
    }

    private function ifOperatorMinus($aStockAdjustment,$stockId) {
        $stock = Stock::find()->andWhere(['id'=>$stockId])->one();
        $stock->stock_adjustment_id = $aStockAdjustment->id;
        $stock->stock_adjustment_status = 1;
        $stock->save(false);
    }

    public function minus($stockId) {

        $stock = Stock::find()->andWhere(['id'=>$stockId])->one();

        $dto = new \stdClass();
        $dto->addressBoxBarcode = $stock->primary_address;
        $dto->productBarcode = $stock->product_barcode;
        $dto->productQuantity = 1;
        $dto->productOperator = "-";
        $dto->reason = "Не нашли на складе";

        $this->change($dto,$stockId);
    }

    /**
     * @param string $LotOrSingleBarcode
     * @param int $Quantity
     * @param string $Operator Возможные значения "+"  или "-"
     * @return array
     */
    public function StockAdjustment($LotOrSingleBarcode,$Quantity,$Operator)
    {
//        $APILogService = new EcommerceAPILogService();
        $request = $this->makeStockAdjustmentRequest($LotOrSingleBarcode,$Quantity,$Operator);
//        $APILogService->StockAdjustmentRequest($request);
        $responseByAPI = $this->api->StockAdjustment($request);
        $preparedResponseByAPI = $this->parseStockAdjustment($responseByAPI);
//        $APILogService->StockAdjustmentResponse($preparedResponseByAPI);

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
}