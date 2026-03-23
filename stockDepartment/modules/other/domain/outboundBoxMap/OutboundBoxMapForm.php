<?php

namespace stockDepartment\modules\other\domain\outboundBoxMap;

use common\modules\outbound\models\OutboundBox;
use stockDepartment\modules\wms\managers\erenRetail\placement\PlacementValidation;
use Yii;
use yii\base\Model;
use common\components\BarcodeManager;

class OutboundBoxMapForm extends Model
{
	private $validation;

    public $fromBox;
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
            [['toBox'], 'validateClientBoxBarcode', 'on' => 'onToBox'],
        ];
    }
    //
    public function validateFromBox($attribute,$params)
    {
        $fromBox = $this->fromBox;
        if(!$this->validation->isBoxNotEmpty($fromBox)) {
			$msg ='<b>[' . $fromBox . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст');
			$this->addError($attribute, '<b>[' . $msg);
        }
    }

    public function validateClientBoxBarcode($attribute,$params)
    {
		$boxBarcode = $this->$attribute;

		$barcode = explode("-",$boxBarcode);
		if (count($barcode) != 2) {
			$this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' ."Это не шк короба клиента");
		}

		$box = OutboundBox::find()
						  ->andWhere(["client_box"=>$boxBarcode])
						  ->andWhere("created_user_id is null")
						  ->one();
		if (!empty($box) && $box->our_box != $this->fromBox) {
			$this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' ."Уже связан с: ".$box->our_box);
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
            'fromBox' => Yii::t('inbound/forms', 'Наш короб'),
            'toBox' => Yii::t('inbound/forms', 'Их короб'),
        ];
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->fromBox = $this->fromBox;
        $dto->toBox = $this->toBox;
        return $dto;
    }
}