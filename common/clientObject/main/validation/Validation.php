<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 12:33
 */

namespace common\clientObject\main\validation;


use common\clientObject\main\inbound\repository\InboundRepository;
use common\components\BarcodeManager;
use common\modules\stock\models\Stock;

class Validation
{
    private $config;
    private $inboundRepository;
    private $productService;
    private $placementUnitService;
    private $inboundUnitAddressService;
    private $stockService;

    /**
     * Validation constructor.
     * @param $config array
     * @param $params array
     */
    public function __construct($config = [],$params = [])
    {
        $this->config = $config;
        $this->inboundRepository = new InboundRepository($params);
        $this->productService = new \common\modules\product\service\ProductService();
        $this->placementUnitService = new \common\modules\placementUnit\service\Service();
        $this->inboundUnitAddressService = new \common\modules\placementUnit\service\InboundUnitAddressService();
        $this->stockService = new \common\modules\stock\service\Service();
    }
    //
    public function isOrderExist($orderNumber)
    {
        return $this->inboundRepository->isOrderExist($orderNumber);
    }
    //
    public function isProductBarcodeExistInOrder($productBarcode, $inboundOrderID)
    {
        return $this->inboundRepository->isProductBarcodeExistInOrder($productBarcode, $inboundOrderID);
    }
    //
    public function isProductModelBarcodeExistInOrder($productModelBarcode, $inboundOrderID)
    {
        return $this->inboundRepository->isProductModelBarcodeExistInOrder($productModelBarcode, $inboundOrderID);
    }
    //
    public function isExtraBarcodeInOrder($inboundID,$barcode) {
        return $this->inboundRepository->IsExtraBarcodeInOrder($inboundID,$barcode);
    }
    //
    public function isTransportedBoxBarcode($transportedBoxBarcode)
    {
        return $this->placementUnitService->isExist($transportedBoxBarcode);
    }
    //
    public function isFreeTransportedBoxBarcode($transportedBoxBarcode,$inboundID)
    {
        return $this->placementUnitService->isFree($transportedBoxBarcode) || $this->placementUnitService->isWorkWithOrder($transportedBoxBarcode,$inboundID);
    }
    //
    public function isWorkTransportedBoxBarcode($transportedBoxBarcode)
    {
        return $this->placementUnitService->isWork($transportedBoxBarcode);
    }
    //
    public function isWorkTransportedBoxBarcodeAndNotEmptyUnitFlow($transportedBoxBarcode)
    {
        return $this->placementUnitService->isWork($transportedBoxBarcode) && $this->placementUnitService->isNotEmptyUnitFlow($transportedBoxBarcode);
    }

    public function isEmptyProductBarcodeByModel($model) {
        return $this->productService->isEmptyProductBarcodeByModel( $this->inboundRepository->getClientID(),$model);
    }

    public function isProductDiffModel($productBarcode,$productModel) {
        return $this->productService->isProductDiffModel($this->inboundRepository->getClientID(),$productBarcode,$productModel);
    }

    public function isInboundUnitAddress($inboundUnitBarcode) {
        return $this->inboundUnitAddressService->isExist($inboundUnitBarcode);
    }

    public function isEmptyAddress($inboundUnitBarcode) {

        $countInStockYes = Stock::find()->andWhere([
            'secondary_address' => $inboundUnitBarcode,
            'status_availability'=> Stock::STATUS_AVAILABILITY_YES
        ])->count();


        if($countInStockYes < 1) {
            return BarcodeManager::isEmptyAddress($inboundUnitBarcode);
        }
        return false;
    }
}