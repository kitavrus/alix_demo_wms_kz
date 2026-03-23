<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\stock\forms;

use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\constants\StockInboundStatus;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\defacto\stock\service\StockAdjustmentService;
use common\ecommerce\entities\EcommerceStock;
use yii\base\Model;
use Yii;
use yii\helpers\VarDumper;

class StockAdjustmentForm extends Model
{
    const SCENARIO_ADD = 'ADD';
    private $service;
    private $barcodeService;
    private $stockService;

    public $productBarcode;
    public $productQuantity;
    public $productOperator;
    public $reason;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->service = new StockAdjustmentService();
        $this->barcodeService = new BarcodeService();
        $this->stockService = new Service();
    }

    /**
     *
     * */
    public function rules()
    {
        return [
            [['productBarcode'], 'trim', 'on' => self::SCENARIO_ADD],
            [['productBarcode'], 'string', 'on' => self::SCENARIO_ADD],
            [['productBarcode'], 'ProductBarcode', 'on' => self::SCENARIO_ADD],
            [['productBarcode'], 'required', 'on' => self::SCENARIO_ADD],

            [['productQuantity'], 'trim', 'on' => self::SCENARIO_ADD],
            [['productQuantity'], 'integer', 'on' => self::SCENARIO_ADD],
            [['productQuantity'], 'ProductQuantity', 'on' => self::SCENARIO_ADD],
            [['productQuantity'], 'required', 'on' => self::SCENARIO_ADD],

            [['productOperator'], 'trim', 'on' => self::SCENARIO_ADD],
            [['productOperator'], 'string', 'on' => self::SCENARIO_ADD],
            [['productOperator'], 'required', 'on' => self::SCENARIO_ADD],

            [['reason'], 'trim', 'on' => self::SCENARIO_ADD],
            [['reason'], 'string', 'on' => self::SCENARIO_ADD],
            [['reason'], 'required', 'on' => self::SCENARIO_ADD],
        ];
    }

    public function ProductBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if (!$this->barcodeService->isDefactoProductBarcode($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Это не штрих-код товара'));
        }
    }

    public function ProductQuantity($attribute, $params)
    {
        $value = $this->$attribute;
        if ($value < 1) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Количество должно быть больше нуляэ'));
        }
    }

    /**
    *
    * */
    public function attributeLabels()
    {
        return [
            'productBarcode' => Yii::t('outbound/forms', 'Шк товара'),
            'productQuantity' => Yii::t('outbound/forms', 'Количество товара'),
            'productOperator' => Yii::t('outbound/forms', 'Добавляем или удоляем товар у нас со стока'),
            'reason' => Yii::t('outbound/forms', 'Причина'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->productBarcode = $this->productBarcode;
        $dto->productQuantity = $this->productQuantity;
        $dto->productOperator = $this->productOperator;
        $dto->reason = $this->reason;
        return $dto;
    }

    public function change() {

        $stockAdjustment = new \common\ecommerce\entities\EcommerceStockAdjustment();
        $stockAdjustment->product_barcode = $this->productBarcode;
        $stockAdjustment->product_quantity = $this->productQuantity;
        $stockAdjustment->product_operator = $this->productOperator;
        $stockAdjustment->reason = $this->reason;
        $stockAdjustment->save(false);

        switch($this->productOperator) {
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
            $this->service->StockAdjustment($this->productBarcode,$this->productQuantity,$this->productOperator);
        }

        return $result;
    }

    private function ifOperatorPlus($aStockAdjustment) {

        for ($i = 1; $i <=  $aStockAdjustment->product_quantity; $i++) {
            // создает DTO для стока
            $dtoForCreateStock = new \stdClass();
            $dtoForCreateStock->clientId = 2;
            $dtoForCreateStock->inboundId = null;
            $dtoForCreateStock->productBarcode = $this->productBarcode;
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