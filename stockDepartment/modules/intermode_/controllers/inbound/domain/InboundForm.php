<?php

namespace app\modules\intermode\controllers\inbound\domain;

use app\modules\intermode\controllers\inbound\domain\InboundOrderValidation;
use common\components\BarcodeManager;
use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;

class InboundForm extends Model {

	public $client_id;
	public $order_number;
	public $product_barcode;
	public $box_barcode;
	public $party_number;

	public $datamatrix;
	public $withDatamatrix = 0;
	public $stockId;
	public $addExtraProduct = 0;
	public $productQty = 1;
    public $conditionType = 1;

	private $validation;

	const SCENARIO_SCAN_DATAMATRIX = 'SCAN-DATAMATRIX';

	//
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->validation = new InboundOrderValidation();
	}

	public function rules()
	{
		return [
			[['client_id','order_number'], 'required'],
			[['client_id'], 'integer'],
			[['order_number','box_barcode','product_barcode','party_number'], 'string'],
			[['box_barcode'], 'validateBoxBarcode'],
			[['box_barcode'], 'validateBoxBarcodeOnly5000'],
			[['box_barcode'], 'trim'],

			[['order_number','box_barcode','party_number'], 'string','on'=>'ScannedBox'],
			[['client_id','order_number','box_barcode'], 'required','on'=>'ScannedBox'],
			[['box_barcode'], 'validateBoxBarcode','on'=>'ScannedBox'],
			[['box_barcode'], 'validateBoxBarcodeOnly5000','on'=>'ScannedBox'],
			[['box_barcode'], 'trim','on'=>'ScannedBox'],

			[['client_id','order_number'], 'required','on'=>'ConfirmOrder'],
			[['client_id','order_number','box_barcode'], 'required','on'=>'ClearBox'],
			[['box_barcode'], 'validateClearBox','on'=>'ClearBox'],
			[['client_id','order_number','box_barcode','product_barcode'], 'required','on'=>'ClearProductInBox'],
			[['product_barcode'], 'validateProductInBox','on'=>'ClearProductInBox'],

			[['product_barcode','box_barcode','order_number',"addExtraProduct"], 'required','on'=>'ScannedProduct'],
			[['product_barcode'], 'validateProductBarcode','on'=>'ScannedProduct'],
			[['box_barcode'], 'validateBoxBarcodeOnly5000','on'=>'ScannedProduct'],

			[['datamatrix','product_barcode', 'box_barcode', 'order_number'], 'required', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['product_barcode'], 'string', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['product_barcode'], 'trim', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['stockId'], 'string', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['stockId'], 'trim', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['datamatrix'], 'string', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['datamatrix'], 'trim', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['box_barcode'], 'validateBoxBarcode', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['box_barcode'], 'validateBoxBarcodeOnly5000', 'on' => self::SCENARIO_SCAN_DATAMATRIX],
			[['datamatrix'], 'validateDatamatrix', 'on' => self::SCENARIO_SCAN_DATAMATRIX],

		];
	}

	/**
	* Validate box_barcode
	* */
	public function validateBoxBarcode($attribute, $params)
	{
		$value = $this->$attribute;
		if(!BarcodeManager::isBox($value)) {
			$this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','Invalid box barcode. Box barcode first letter must be b'));
		}

		$inbound_order_id = $this->order_number;
		$count =  Stock::find()
					   ->andWhere(['primary_address'=>$value,
						   'status'=>[
							   Stock::STATUS_INBOUND_SCANNING,
							   Stock::STATUS_INBOUND_SCANNED,
							   Stock::STATUS_INBOUND_OVER_SCANNED
						   ]
					   ])
					   ->andWhere('inbound_order_id != :inbound_order_id',[':inbound_order_id'=>$inbound_order_id])->exists();

		if($count) {
			$this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','В этом коробе есть товары из другого заказа'));
		}

		$count =  Stock::find()
					   ->andWhere(['primary_address'=>$value,
//                        'status'=>[
//                                    Stock::STATUS_INBOUND_SCANNING,
//                                    Stock::STATUS_INBOUND_SCANNED,
//                                    Stock::STATUS_INBOUND_OVER_SCANNED
//                        ]
					   ])
					   ->andWhere('inbound_order_id != :inbound_order_id AND secondary_address != ""',[':inbound_order_id'=>$inbound_order_id])->exists();

		if($count) {
			$this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','В этом коробе есть товары из другого заказа и он уже размещен'));
		}
	}

	/*
	 * Validate product_barcode
	 * */
	public function validateProductBarcode($attribute, $params)
	{
		$productBarcode = $this->$attribute;
		$orderNumberId = $this->order_number;
		//  НЕ ПРИНИМАЕМ ПЛЮСЫ
		if($this->addExtraProduct == 1) {
			return;
		}

		if(!self::checkProductBarcode($productBarcode,$orderNumberId)) {
			$this->addError($attribute, '<b> [ ' . $productBarcode . ' ] </b> '.Yii::t('inbound/errors','Scanned product barcode not found in selected inbound'));
		}

		if ($this->validation->isExtraBarcodeInOrder($productBarcode, $orderNumberId)) {
			$this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это лишний товар в накладной'));
		}

	}

	/*
	 * Remove product in box
	 *
	 * */
	public function validateProductInBox($attribute, $params)
	{
		$productBarcode = $this->$attribute;
		$box_barcode = $this->box_barcode;
		$orderNumberId = $this->order_number;
		if(!self::checkProductInBox($productBarcode,$box_barcode)) {
			$this->addError($attribute, '<b> [ ' . $productBarcode . ' ] </b> '.Yii::t('inbound/errors','Короб пуст')); // Этого товара нет в укзанном коробе
		}
//        if($product = self::checkProductInBox($value,$box_barcode)){
//            if($product->outbound_order_id){
//                $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This product already assigned to outbound order')); // Этот товар уже привязан к outbound order
//            }
//        }
		if(InboundOrder::find()->andWhere(['status'=>Stock::STATUS_INBOUND_COMPLETE,'id'=> $orderNumberId])->exists()) {
			$this->addError($attribute, '<b> [ ' . $productBarcode . ' ] </b> '.Yii::t('inbound/errors','This order is complete'));
		}

//		if ($this->validation->isExtraBarcodeInOrder($productBarcode,$orderNumberId)) {
//			$this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это лишний товар в накладной'));
//		}

	}

	/*
	 * Remove all product in box
	 *
	 * */
	public function validateClearBox($attribute, $params)
	{
		$value = $this->$attribute;
//        $box_barcode = $this->box_barcode;
//        if(!self::checkProductInBox($value,$box_barcode)) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Box is empty')); // Этого товара нет в укзанном коробе
//        }

		if(InboundOrder::find()->andWhere(['status'=>Stock::STATUS_INBOUND_COMPLETE,'id'=> $this->order_number])->exists()) {
			$this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This order is complete'));
		}

	}

	public function attributeLabels()
	{
		return [
			'party_number' => Yii::t('inbound/forms', 'Party number'),
			'client_id' => Yii::t('inbound/forms', 'Client'),
			'order_number' => Yii::t('inbound/forms', 'Order number'),
			'box_barcode' => Yii::t('inbound/forms', 'Box barcode'),
			'product_barcode' => Yii::t('inbound/forms', 'Product barcode'),
		];
	}



	/*
	 * Проверяет существует ли отсканированный товар в выбранном заказе
	 * @param string $productBarcode
	 * @param integer $inbound_order_id
	 * @return
	 * */
	public function checkProductBarcode($productBarcode,$inbound_order_id)
	{
		return InboundOrderItem::find()->andWhere(['product_barcode'=>$productBarcode,'inbound_order_id'=>$inbound_order_id])->exists();
	}

	/*
	* Check exist product in box
	* @param string $productBarcode
	* @return boolean
	* */
	public function checkProductInBox($productBarcode,$box_barcode)
	{
		return Stock::find()->andWhere(['primary_address'=>$box_barcode,'product_barcode'=>$productBarcode,'status'=>[Stock::STATUS_INBOUND_SCANNED,Stock::STATUS_INBOUND_OVER_SCANNED]])->exists();
//        return InboundOrderItemProcess::find()->where(['box_barcode'=>$box_barcode,'product_barcode'=>$productBarcode])->exists();
	}

	/**
	 * Validate box_barcode
	 * */
	public function validateBoxBarcodeOnly5000($attribute, $params){
		$boxBarcode = $this->box_barcode;
		$inboundError = BarcodeManager::isValidInboundBoxBarcode($boxBarcode);
		if ($inboundError) {
			$this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', $inboundError));
		}
	}

	//
	public function getDTO() {
		$dto = new \stdClass();
		$dto->orderNumberId = $this->order_number;
		$dto->ourBoxBarcode = BarcodeService::onlyDigital($this->box_barcode);
		$dto->conditionType = $this->conditionType;
		$dto->productBarcode = BarcodeService::onlyDigital($this->product_barcode);
		$dto->productQty = BarcodeService::onlyDigital($this->productQty);
		$dto->addExtraProduct = $this->addExtraProduct;
		$dto->datamatrix = $this->datamatrix;
		$dto->stockId = BarcodeService::onlyDigital($this->stockId);
		return $dto;
	}

	public function validateDatamatrix($attribute, $params)
	{
		$orderNumberId = $this->order_number;
		$productBarcode = $this->product_barcode;
		$datamatrix = $this->datamatrix;
		$stockId = $this->stockId;
		if($this->validation->isNotAvailableDataMatrix($orderNumberId,$productBarcode,$datamatrix)) {
			$this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Нет доступных ДатаМатриц'));
		}

		if($this->validation->checkExistDataMatrixByStockId($stockId,"",$datamatrix)) {
			$this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Эта дата-матрица уже добавлена'));
		}
	}

	/**
	* Находим отсканированный шк в заказе и ставим статус отсканирован
	 * @return Stock
	* */
	public function setScannedStatus()
	{
		$stock = Stock::find()->andWhere([
			'inbound_order_id' => $this->order_number,
			'product_barcode' => $this->product_barcode,
//			'inbound_client_box' => $this->client_box_barcode,
			'status' => [
				Stock::STATUS_INBOUND_NEW,
				Stock::STATUS_INBOUND_SCANNING,
			],
			'client_id' => $this->client_id,
		])->one();

		if($stock) {
			$stock->status = Stock::STATUS_INBOUND_SCANNED;
			$stock->primary_address = $this->box_barcode;
			$stock->status_availability  = Stock::STATUS_AVAILABILITY_NOT_SET;
			$stock->scan_in_datetime = time();
			$stock->save(false);
		} else {
			$stock = new Stock();
			$attributes = [
				'scan_in_datetime'=>time(),
				'client_id'=>$this->client_id,
				'inbound_order_id'=>$this->order_number,
				'product_barcode'=>$this->product_barcode,
				'primary_address'=>$this->box_barcode,
				//'inbound_client_box'=>$this->client_box_barcode,
				'status'=>Stock::STATUS_INBOUND_SCANNED,
				'status_availability'=>Stock::STATUS_AVAILABILITY_NOT_SET,
				'system_status'=>$this->client_id.'-'.'OVER-SCAN',
				'system_status_description'=>'Это товар которога не должно быть в этом коробе. Но по факту он есть',
			];
			$stock->setAttributes($attributes,false);
			$stock->save(false);


			$inboundItemOne = InboundOrderItem::find()
											  ->andWhere([
												  'inbound_order_id'=>$this->order_number,
												  'product_barcode'=>$this->product_barcode,
												 //'box_barcode'=>$this->client_box_barcode,
											  ])
											  ->one();

			if(!$inboundItemOne) {
				$inboundItemOne = new InboundOrderItem();
				$inboundItemOne->inbound_order_id = $this->order_number;
				$inboundItemOne->product_barcode = $this->product_barcode;
				$inboundItemOne->accepted_qty = 1;
				//$inboundItemOne->box_barcode = $this->client_box_barcode;
				$inboundItemOne->save(false);
			}

			$stock->inbound_order_item_id = $inboundItemOne->id;
			$stock->product_sku = $inboundItemOne->product_sku;
			$stock->product_id = $inboundItemOne->product_id;
			$stock->product_name = $inboundItemOne->product_name;
			$stock->product_color = $inboundItemOne->product_color;
			$stock->product_brand = $inboundItemOne->product_brand;
			$stock->save(false);

		}
		//$this->setStockModel($stock);
		return $stock;
	}
}