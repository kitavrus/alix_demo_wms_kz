<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;

use common\clientObject\main\inbound\validation\BoxToBoxValidation;
use common\modules\stock\service\ChangeAddressPlaceService;
use Yii;
use yii\base\Model;
use common\components\BarcodeManager;

class BoxToBoxForm extends Model
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
			
            [['fromBox','toBox'], 'required', 'on' => 'onToBox'],
            [['toBox'], 'string', 'on' => 'onToBox'],
            [['toBox'], 'trim', 'on' => 'onToBox'],
            [['toBox'], 'validateBoxBarcodeOnly5000', 'on' => 'onToBox'],
            [['toBox'], 'validateToBox', 'on' => 'onToBox'],
        ];
    }
    //
    public function validateFromBox($attribute,$params)
    {
        $fromBox = $this->fromBox;
        if(!$this->validation->isBoxNotEmpty($fromBox)) {
			$msg ='<b>[' . $fromBox . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст');
			ChangeAddressPlaceService::add($fromBox,"-empty-",$msg);
			$this->addError($attribute, '<b>[' . $msg);
        }
    }

    public function validateToBox($attribute,$params)
    {
//        $toBox = $this->toBox;
//        if(!$this->validation->isBoxOnPlace($toBox)) {
//            $this->addError($attribute, '<b>[' . $toBox . ']</b> ' . Yii::t('inbound/errors', 'Этот короб не размещен'));
//        }
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
            'toBox' => Yii::t('inbound/forms', 'В короб'),
        ];
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->fromBox = $this->fromBox;
        $dto->toBox = $this->toBox;
        return $dto;
    }
}