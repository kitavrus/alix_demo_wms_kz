<?php
namespace stockDepartment\modules\wms\managers\erenRetail\outbound_data_matrix;

use common\components\BarcodeManager;
use yii\base\Model;
use Yii;
use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\managers\erenRetail\outbound_data_matrix\OutboundDataMatrixService;

class OutboundDataMatrixForm extends Model {
	public $box_barcode;
	public $product_barcode;
	public $product_datamatrix;

	/*
	 *
	 * */
	public function rules()
	{
		return [
			// [['box_barcode','product_datamatrix'], 'trim'],
			//[['box_barcode','product_datamatrix'], 'string'],
			[['box_barcode'],'IsBoxBarcode', 'on'=>'IsBoxBarcode'],
			[['box_barcode'],'required', 'on'=>'IsBoxBarcode'],


			[['product_barcode'],'validateProductBarcode', 'on'=>'IsProductBarcode'],
			[['box_barcode'],'IsBoxBarcode', 'on'=>'IsProductBarcode'],
			[['product_barcode'],'required', 'on'=>'IsProductBarcode'],
			[['box_barcode'],'required', 'on'=>'IsProductBarcode'],

			[['product_datamatrix'],'validateProductDatamatrix', 'on'=>'IsProductDatamatrix'],
			[['box_barcode'],'IsBoxBarcode', 'on'=>'IsProductDatamatrix'],
			[['product_datamatrix'],'required', 'on'=>'IsProductDatamatrix'],
			[['product_barcode'],'required', 'on'=>'IsProductDatamatrix'],
			[['box_barcode'],'required', 'on'=>'IsProductDatamatrix'],

			[['box_barcode'], 'required','on'=>'ClearBox'],
			[['box_barcode'], 'IsBoxBarcode','on'=>'ClearBox'],
			[['box_barcode'], 'validateClearBox','on'=>'ClearBox'],
		];
	}

	/*
	*
	* */
	public function validateProductBarcode($attribute, $params)
	{
		$box_barcode = $this->box_barcode;
		$product_barcode = $this->product_barcode;
		$service = new OutboundDataMatrixService();
		if(!$service->checkProductInBox($box_barcode,$product_barcode)) {
			$this->addError($attribute, '<b> [ ' . $box_barcode . ' ] </b> '.Yii::t('outbound/errors','Этого товара нет в выбранном коробе ['.$box_barcode.']'));
		}
	}

	/*
	*
	* */
	public function validateProductDatamatrix($attribute, $params)
	{
		if(mb_strlen($this->product_datamatrix,'UTF-8') != 127) {
			$this->addError($attribute, '<b> [ ' . $this->product_datamatrix . ' ] </b> '.Yii::t('outbound/errors','Этот QR неправильный длинны ['.mb_strlen($this->product_datamatrix,'UTF-8').']'));
		}
	}


	/*
	* Validate barcode picking list
	* */
	public function IsBoxBarcode($attribute, $params)
	{
		$value = $this->$attribute;
		if(!BarcodeManager::isBox($value)) {
			$this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод короба'));
		}
	}

	/*
	 *
	 * */
	public function validateClearBox($attribute, $params)
	{
		$value = $this->$attribute;
		if( !Stock::find()->where([
			'status'=>Stock::STATUS_OUTBOUND_SCANNED,
		])->count()) {
			$this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','"Этот короб пуст или для него уже распечатали этикетки'));
		}
	}

	/*
	*
	*
	* */
	public function attributeLabels()
	{
		return [
			'box_barcode' => Yii::t('outbound/forms', 'Box barcode'),
			'product_datamatrix' => Yii::t('outbound/forms', 'Product qr code'),
		];
	}
}