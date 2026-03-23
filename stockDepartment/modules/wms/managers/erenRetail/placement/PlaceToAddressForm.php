<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;

use Yii;
use yii\base\Model;
use common\modules\stock\service\ChangeAddressPlaceService;
use common\components\BarcodeManager;

class PlaceToAddressForm extends Model
{
    private $validation;

    public $fromPlaceAddress; // From address
    public $toPlaceAddress; // To address

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
            // From place address
            [['fromPlaceAddress'], 'required', 'on' => 'onFromAddress'],
            [['fromPlaceAddress'], 'string', 'on' => 'onFromAddress'],
            [['fromPlaceAddress'], 'trim', 'on' => 'onFromAddress'],
			[['fromPlaceAddress'], 'validateBoxBarcodeOnly5000', 'on' => 'onFromAddress'],
            [['fromPlaceAddress'], 'validateFromAddress', 'on' => 'onFromAddress'],
            // To place address
            [['fromPlaceAddress', 'toPlaceAddress'], 'required', 'on' => 'onToPlaceAddress'],
            [['toPlaceAddress'], 'string', 'on' => 'onToPlaceAddress'],
            [['toPlaceAddress'], 'trim', 'on' => 'onToPlaceAddress'],
            [['toPlaceAddress'], 'validateToPlaceAddress', 'on' => 'onToPlaceAddress'],
        ];
    }
    //
    public function validateFromAddress($attribute,$params)
    {
        $fromPlaceAddress = $this->fromPlaceAddress;
        if(!$this->validation->isBox($fromPlaceAddress)) {
        	$msg = '<b>[' . $fromPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Это не шк короба');
			ChangeAddressPlaceService::add($fromPlaceAddress,"-empty-",$msg);
            $this->addError($attribute, '<b>[' . $msg);
        }
		if(!$this->validation->isBoxNotEmpty($fromPlaceAddress)) {
			$msg = '<b>[' . $fromPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст');
			ChangeAddressPlaceService::add($fromPlaceAddress,"-empty-",$msg);
			$this->addError($attribute,$msg );
		}
    }
    //
    public function validateToPlaceAddress($attribute, $params)
    {
        $fromPlaceAddress = $this->fromPlaceAddress;
        $toPlaceAddress = $this->toPlaceAddress;

		if(!$this->validation->isBox($fromPlaceAddress)) {
			$msg ='<b>[' . $fromPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Это не шк короба');
			ChangeAddressPlaceService::add($fromPlaceAddress,$toPlaceAddress,$msg);
			$this->addError($attribute, $msg);
		}

		if(!$this->validation->isPlace($toPlaceAddress)) {
			$msg = '<b>[' . $toPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Это не полка');
			ChangeAddressPlaceService::add($fromPlaceAddress,$toPlaceAddress,$msg);
			$this->addError($attribute, $msg);
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
            'fromPlaceAddress' => Yii::t('inbound/forms', 'Шк приходного короба'),
            'toPlaceAddress' => Yii::t('inbound/forms', 'Адрес полки'),
        ];
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->fromPlaceAddress = $this->fromPlaceAddress;
        $dto->toPlaceAddress = $this->toPlaceAddress;
        return $dto;
    }
}