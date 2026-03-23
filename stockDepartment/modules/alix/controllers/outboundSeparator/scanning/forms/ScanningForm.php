<?php
namespace stockDepartment\modules\alix\controllers\outboundSeparator\scanning\forms;

use common\components\BarcodeManager;
use stockDepartment\modules\alix\controllers\outboundSeparator\scanning\dto\FormDTO;
use stockDepartment\modules\alix\controllers\outboundSeparator\scanning\service\FormService;
use yii\base\Model;
use Yii;

class ScanningForm extends Model
{
    public $outbound_separator_id;
    public $out_box_barcode;
    public $in_box_barcode;
    public $product_barcode;
    private $outboundSeparatorService;

    const SCENARIO_OUTBOUND_SEPARATOR = 'OUTBOUND_SEPARATOR';
    const SCENARIO_OUT_BOX_BARCODE = 'OUT_BOX_BARCODE';
    const SCENARIO_IN_BOX_BARCODE = 'IN_BOX_BARCODE';
    const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';

    public function __construct($config = [])
    {
        parent::__construct($config);
		$this->outboundSeparatorService = new FormService();
    }

    /**
	 *
     * */
    public function rules()
    {
        return [
            [['in_box_barcode', 'out_box_barcode', 'product_barcode'], 'trim'],
            [['in_box_barcode', 'out_box_barcode', 'product_barcode'], 'string'],
            //
            [['outbound_separator_id'], 'validateOutboundSeparator', 'on' => self::SCENARIO_OUTBOUND_SEPARATOR],
            [['outbound_separator_id'], 'required', 'on' => self::SCENARIO_OUTBOUND_SEPARATOR],
            //
			[['in_box_barcode'], 'validateInBoxBarcode', 'on' => self::SCENARIO_IN_BOX_BARCODE],
			[['in_box_barcode'], 'validateInBoxBarcodeOnly5000', 'on' => self::SCENARIO_IN_BOX_BARCODE],
			[['outbound_separator_id','in_box_barcode'], 'required', 'on' => self::SCENARIO_IN_BOX_BARCODE],
			//
			[['out_box_barcode'], 'validateOutBoxBarcode', 'on' => self::SCENARIO_OUT_BOX_BARCODE],
			[['out_box_barcode'], 'validateInBoxBarcodeOnly4000', 'on' => self::SCENARIO_OUT_BOX_BARCODE],
			[['outbound_separator_id','in_box_barcode','out_box_barcode'], 'required', 'on' => self::SCENARIO_OUT_BOX_BARCODE],
			//
			[['product_barcode'], 'validateProductBarcode', 'on' => self::SCENARIO_PRODUCT_BARCODE],
			[['outbound_separator_id','in_box_barcode','out_box_barcode',"product_barcode"], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
        ];
    }

    /**
    * Validate
    * */
    public function validateOutboundSeparator($attribute, $params)
    {

    }

    /**
    * Validate
    * */
    public function validateInBoxBarcode($attribute, $params)
    {
//		$outbound_separator_id = $this->outbound_separator_id;
//		$box_barcode = $this->box_barcode;
//		if ($this->outboundSeparatorService->canScannedBox($outbound_separator_id,$box_barcode)) {
//			$this->addError($attribute, '<b>[' . $box_barcode . ']</b> ' . Yii::t('outbound/errors', 'В коробе нечего извлекать'));
//		}
    }

	/**
	 * Validate box_barcode
	 * */
	public function validateInBoxBarcodeOnly5000($attribute, $params){
		$boxBarcode = $this->in_box_barcode;
		$inboundError = BarcodeManager::isValidInboundBoxBarcode($boxBarcode);
		if ($inboundError) {
			$this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', $inboundError));
		}
	}

    /**
    * Validate
    * */
    public function validateOutBoxBarcode($attribute, $params)
    {

    }

	/**
	 * Validate box_barcode
	 * */
	public function validateInBoxBarcodeOnly4000($attribute, $params){
		$boxBarcode = $this->out_box_barcode;
		$inboundError = BarcodeManager::isValidOutboundBoxBarcode($boxBarcode);
		if ($inboundError) {
			$this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', $inboundError));
		}
	}

    /**
    * Validate
    * */
    public function validateProductBarcode($attribute, $params)
    {
        $outbound_separator_id = $this->outbound_separator_id;
        $out_box_barcode = $this->out_box_barcode;
		$product_barcode = $this->product_barcode;
        if ($this->outboundSeparatorService->canScannedProduct($outbound_separator_id,$product_barcode,$out_box_barcode)) {
            $this->addError($attribute, '<b>[' . $product_barcode . ']</b> ' . Yii::t('outbound/errors', 'Нет товара на извлечение'));
        }
    }

    /**
    *
    * */
    public function attributeLabels()
    {
        return [
            'outbound_separator_id' => Yii::t('outbound/forms', 'Накладная на извлечение'),
            'out_box_barcode' => Yii::t('outbound/forms', 'Отгрузочный короб 4xxx'),
            'in_box_barcode' => Yii::t('outbound/forms', 'Короб для размещения 5xxx'),
            'product_barcode' => Yii::t('outbound/forms', 'Товар'),
        ];
    }

    public function getDTO()
    {
        return new FormDTO(
        	$this->outbound_separator_id,
			$this->in_box_barcode,
			$this->out_box_barcode,
			$this->product_barcode
		);
    }


	public function getInfoByOrder() {
		$dto = $this->getDTO();
		return$this->outboundSeparatorService->getInfoByOrder($dto->id);
	}

    public function getInBoxInfo() {
    	$dto = $this->getDTO();
		return $this->outboundSeparatorService->getInfoByInBoxBarcode($dto->id,$dto->in_box_barcode);
	}

	public function getOutBoxInfo() {
    	$dto = $this->getDTO();
		return $this->outboundSeparatorService->getInfoByOutBoxBarcode($dto->id,$dto->out_box_barcode);
	}

	public function scannedProductOnStock() {
    	$dto = $this->getDTO();
		$this->outboundSeparatorService->scannedProductOnStock($dto->id,$dto->product_barcode,$dto->out_box_barcode,$dto->in_box_barcode);
	}

	public function getActiveOutboundSeparator() {
		return $this->outboundSeparatorService->getActiveOutboundSeparator();
	}

}