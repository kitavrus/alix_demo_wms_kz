<?php
namespace app\modules\ecommerce\controllers\intermode\inbound\domain\forms;

use app\modules\ecommerce\controllers\intermode\inbound\domain\validation\InboundOrderValidation;
use app\modules\ecommerce\controllers\intermode\barcode\domain\service\BarcodeService;
use app\modules\ecommerce\controllers\intermode\inbound\domain\InboundAPIService;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;

class ScanInboundForm extends Model
{
    private $validation;

    public $orderNumberId;
    public $productBarcode;
    public $clientBoxBarcode;
    public $ourBoxBarcode;
    public $productQty;
    public $addExtraProduct = 0;
    public $conditionType;
    public $datamatrix;
    public $stockId;
    public $withDatamatrix = 0;

    const SCENARIO_ORDER_NUMBER = 'ORDER-NUMBER';
    const SCENARIO_OUR_BOX_BARCODE = 'OUR-BOX-BARCODE';
    const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';
    const SCENARIO_CLEAN_OUR_BOX = 'CLEAN-OUR-BOX';
    const SCENARIO_SHOW_ORDER_ITEMS = 'SHOW-ORDER-ITEMS';
    const SCENARIO_PRINT_DIFF_IN_ORDER = 'PRINT-DIFF-IN-ORDER';
    const SCENARIO_CLOSE_ORDER = 'CLOSE-ORDER';
    const SCENARIO_SCAN_DATAMATRIX = 'SCAN-DATAMATRIX';

    //
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->validation = new InboundOrderValidation();
    }
    //
    public function rules()
    {
        return [
            // Select order
            [['orderNumberId'], 'required', 'on' =>self::SCENARIO_ORDER_NUMBER],
            [['orderNumberId'], 'integer', 'on' =>self::SCENARIO_ORDER_NUMBER],
            // Scan out box
            [['ourBoxBarcode', 'orderNumberId'], 'required', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['ourBoxBarcode'], 'string', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['ourBoxBarcode'], 'trim', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['ourBoxBarcode'], 'validateOurBoxBarcode', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            // Scan product
            [['productBarcode', 'ourBoxBarcode', 'orderNumberId','conditionType','addExtraProduct'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'string', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'trim', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'validateProduct', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['ourBoxBarcode'], 'validateOurBoxBarcode', 'on' => self::SCENARIO_PRODUCT_BARCODE],

			// Scan SCENARIO_SCAN_DATAMATRIX
            [['datamatrix','productBarcode', 'ourBoxBarcode', 'orderNumberId','conditionType','addExtraProduct'], 'required', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
            [['productBarcode'], 'string', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['productBarcode'], 'trim', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['stockId'], 'string', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['stockId'], 'trim', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['datamatrix'], 'string', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
            [['datamatrix'], 'trim', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
            [['ourBoxBarcode'], 'validateOurBoxBarcode', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
            [['datamatrix'], 'validateDatamatrix', 'on' => self::SCENARIO_SCAN_DATAMATRIX],

            // Clean transporter box
            [['ourBoxBarcode', 'orderNumberId'], 'required', 'on' => self::SCENARIO_CLEAN_OUR_BOX],
            [['ourBoxBarcode'], 'string', 'on' => self::SCENARIO_CLEAN_OUR_BOX],
            [['ourBoxBarcode'], 'trim', 'on' => self::SCENARIO_CLEAN_OUR_BOX],
            [['ourBoxBarcode'], 'validateOurBoxBarcode', 'on' => self::SCENARIO_CLEAN_OUR_BOX],
            // Show order items
            [['orderNumberId'], 'required', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],
            // Print diff in order
            [['orderNumberId'], 'required', 'on' => self::SCENARIO_PRINT_DIFF_IN_ORDER],
            // Close order
            [['orderNumberId'], 'required', 'on' => self::SCENARIO_CLOSE_ORDER],
            [['orderNumberId'], 'string', 'on' => self::SCENARIO_CLOSE_ORDER],
            [['orderNumberId'], 'trim', 'on' => self::SCENARIO_CLOSE_ORDER],
            [['orderNumberId'], 'validateCloseOrder', 'on' => self::SCENARIO_CLOSE_ORDER],
        ];
    }
    //

	public function validateDatamatrix($attribute, $params)
	{
		$orderNumberId = $this->orderNumberId;
		$productBarcode = $this->productBarcode;
		$datamatrix = $this->datamatrix;
		$stockId = $this->stockId;
		if($this->validation->isNotAvailableDataMatrix($orderNumberId,$productBarcode,$datamatrix)) {
			$this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Нет доступных ДатаМатриц'));
		}

		if($this->validation->checkExistDataMatrixByStockId($stockId,"",$datamatrix)) {
			$this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Эта дата-матрица уже добавлена'));
		}
	}

    //
    public function validateOurBoxBarcode($attribute, $params)
    {
        $ourBoxBarcode = $this->ourBoxBarcode;
		 $inboundId = $this->orderNumberId;

        if(!$this->validation->isOurInboundBoxBarcode($ourBoxBarcode)) {
            $this->addError($attribute, '<b>[' . $ourBoxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это не наш короб'));
        }
		
		if($this->validation->isUsedBox($inboundId,$ourBoxBarcode)) {
            $this->addError($attribute, '<b>[' . $ourBoxBarcode . ']</b> ' . Yii::t('inbound/errors', 'В этом коробе есть товары из другого прихода'));
        }
    }

    public function validateProduct($attribute, $params)
    {
        $inboundId = $this->orderNumberId;
        $productBarcode = $this->productBarcode;

		if ($this->validation->isProductBarcodeExistInOrder($inboundId, $productBarcode)) {
			$this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Этого товара не в накладной'));
		}
		if ($this->validation->isExtraBarcodeInOrder($inboundId, $productBarcode)) {
			$this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это лишний товар в накладной'));
		}
    }

    //
    public function validateCloseOrder($attribute, $params)
    {

    }
    //
    public function getDTO() {
        $dto = new \stdClass();
        $dto->orderNumberId = $this->orderNumberId;
        $dto->ourBoxBarcode = BarcodeService::onlyDigital($this->ourBoxBarcode);
        $dto->conditionType = $this->conditionType;
        $dto->productBarcode = BarcodeService::onlyDigital($this->productBarcode);
        $dto->productQty = BarcodeService::onlyDigital($this->productQty);
        $dto->addExtraProduct = $this->addExtraProduct;
        $dto->datamatrix = $this->datamatrix;
        $dto->stockId = BarcodeService::onlyDigital($this->stockId);
        return $dto;
    }

    //
    public function attributeLabels()
    {
        return [
            'orderNumberId' => Yii::t('inbound/forms', 'Номер партии'),
            'ourBoxBarcode' => Yii::t('inbound/forms', 'ШК нашего короба (CLEAN-BOX)'),
            'productBarcode' => Yii::t('inbound/forms', 'ШК товара (готово)'),
            'conditionType' => Yii::t('inbound/forms', 'Состояние'),
            'withDatamatrix' => Yii::t('inbound/forms', 'С/без дата матрицами'),
        ];
    }
}