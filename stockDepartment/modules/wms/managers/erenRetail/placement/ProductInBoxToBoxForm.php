<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;

use common\clientObject\main\inbound\validation\BoxToBoxValidation;
use Yii;
use yii\base\Model;
use common\components\BarcodeManager;

class ProductInBoxToBoxForm extends Model
{
    private $validation;

    public $fromBox;
    public $productBarcode;
    public $toBox;

    //
    public function __construct($config = [])
    {
        parent::__construct($config);
		$this->validation = new PlacementValidation();
    }
    //
    public function rules()
    {
        return [
            // From Box
            [['fromBox'], 'required', 'on' => 'onFromBox'],
            [['fromBox'], 'string', 'on' => 'onFromBox'],
            [['fromBox'], 'trim', 'on' => 'onFromBox'],
            [['fromBox'], 'validateBoxBarcodeOnly5000', 'on' => 'onFromBox'],
            [['fromBox'], 'validateFromBox', 'on' => 'onFromBox'],
			// To box
			[['fromBox','toBox'], 'required', 'on' => 'onToBox'],
			[['toBox'], 'string', 'on' => 'onToBox'],
			[['toBox'], 'trim', 'on' => 'onToBox'],
			[['toBox'], 'validateBoxBarcodeOnly5000', 'on' => 'onToBox'],
			[['toBox'], 'validateToBox', 'on' => 'onToBox'],
            // Product barcode
            [['fromBox', 'productBarcode','toBox'], 'required', 'on' => 'onProductBarcode'],
            [['productBarcode'], 'string', 'on' => 'onProductBarcode'],
            [['productBarcode'], 'trim', 'on' => 'onProductBarcode'],
            [['productBarcode'], 'validateProductBarcode', 'on' => 'onProductBarcode'],

        ];
    }
    //
    public function validateFromBox($attribute,$params)
    {
        $fromBox = $this->fromBox;

        if(!$this->validation->isBoxNotEmpty($fromBox)) {
            $this->addError($attribute, '<b>[' . $fromBox . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст'));
        }
    }
    //
    public function validateProductBarcode($attribute, $params)
    {
        $fromBox = $this->fromBox;
        $productBarcode = $this->productBarcode;

        if(!$this->validation->isProductExistInBox($productBarcode,$fromBox)) {
            $this->addError($attribute, '<b>[' . $fromBox ." / ".$productBarcode. ']</b> ' . Yii::t('inbound/errors', 'Этого товара нет в этом коробе'));
        }
    }
    //
    public function validateToBox($attribute,$params)
    {
        $toBox = $this->toBox;
        if(!$this->validation->isBoxOnPlace($toBox)) {
           // $this->addError($attribute, '<b>[' . $toBox . ']</b> ' . Yii::t('inbound/errors', 'Этот короб не размещен'));
        }
    }
	
	/**
	 * Validate box_barcode
	 * */
	public function validateBoxBarcodeOnly5000($attribute, $params){
		$boxBarcode = $this->$attribute;
		$inboundError = BarcodeManager::isValidInboundBoxBarcode($boxBarcode);
		if ($inboundError) {
			$this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', $inboundError));
		}
	}
    //
    public function attributeLabels()
    {
        return [
            'fromBox' => Yii::t('inbound/forms', 'Из короба'),
            'productBarcode' => Yii::t('inbound/forms', 'ШК товара'),
            'toBox' => Yii::t('inbound/forms', 'В короб'),
        ];
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->fromBox = $this->fromBox;
        $dto->productBarcode = $this->productBarcode;
        $dto->toBox = $this->toBox;
        return $dto;
    }
}