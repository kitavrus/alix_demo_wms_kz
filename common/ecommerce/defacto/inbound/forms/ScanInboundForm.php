<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */

namespace common\ecommerce\defacto\inbound\forms;

use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\defacto\inbound\service\InboundAPIService;
use common\ecommerce\defacto\inbound\validation\InboundOrderValidation;
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
//    public $lotBarcode;
    public $productQty;
    public $addExtraProduct = 0;
    public $conditionType;

    const SCENARIO_ORDER_NUMBER = 'ORDER-NUMBER';
    const SCENARIO_CLIENT_BOX_BARCODE = 'CLIENT-BOX-BARCODE';
    const SCENARIO_OUR_BOX_BARCODE = 'OUR-BOX-BARCODE';
//    const SCENARIO_LOT_BARCODE = 'LOT-BARCODE';
    const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';
//    const SCENARIO_PRODUCT_QTY = 'PRODUCT-QTY';
    const SCENARIO_CLEAN_OUR_BOX = 'CLEAN-OUR-BOX';
    const SCENARIO_SHOW_ORDER_ITEMS = 'SHOW-ORDER-ITEMS';
    const SCENARIO_PRINT_DIFF_IN_ORDER = 'PRINT-DIFF-IN-ORDER';
    const SCENARIO_CLOSE_ORDER = 'CLOSE-ORDER';

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
            // Scan client box
            [['clientBoxBarcode', 'orderNumberId'], 'required', 'on' => self::SCENARIO_CLIENT_BOX_BARCODE],
            [['clientBoxBarcode'], 'string', 'on' => self::SCENARIO_CLIENT_BOX_BARCODE],
            [['clientBoxBarcode'], 'trim', 'on' => self::SCENARIO_CLIENT_BOX_BARCODE],
            [['clientBoxBarcode'], 'validateClientBoxBarcode', 'on' => self::SCENARIO_CLIENT_BOX_BARCODE],
            // Scan out box
            [['ourBoxBarcode', 'orderNumberId'], 'required', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['ourBoxBarcode'], 'string', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['ourBoxBarcode'], 'trim', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['ourBoxBarcode'], 'validateOurBoxBarcode', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            // Scan product
            [['productBarcode', 'clientBoxBarcode','ourBoxBarcode', 'orderNumberId','conditionType','addExtraProduct'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'string', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'trim', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'validateProduct', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['ourBoxBarcode'], 'validateOurBoxBarcode', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            // Clean transporter box
            [['ourBoxBarcode', 'clientBoxBarcode', 'orderNumberId'], 'required', 'on' => self::SCENARIO_CLEAN_OUR_BOX],
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

    //
    public function validateClientBoxBarcode($attribute, $params)
    {
        $clientBoxBarcode = $this->clientBoxBarcode;
        $orderNumberId = $this->orderNumberId;


        if(!$this->validation->isDefactoBoxBarcode($clientBoxBarcode)) {
            $this->addError($attribute, '<b>[' . $clientBoxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это не короб дефакто'));
        }
		
		if($this->validation->isClientBoxExistInOtherOrder($orderNumberId,$clientBoxBarcode)) {
            $this->addError($attribute, '<b>[' . $clientBoxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это не короб уже принят в другую накладную'));
        }

        if($this->validation->isDefactoBoxBarcode($clientBoxBarcode) && !$this->validation->isClientBarcodeExistInOrder($orderNumberId,$clientBoxBarcode)) {
           $api = new InboundAPIService();
           $response = $api->get($clientBoxBarcode,$orderNumberId);
           if($response['HasError'] != false) {
                $this->addError($attribute, '<b>[' . $clientBoxBarcode . ']</b> Нет данных от дефакто. ' . $response['ErrorMessage']);
           }
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
        $clientBoxBarcode = $this->clientBoxBarcode;
//        $lotBarcode = $this->lotBarcode;
        $productBarcode = $this->productBarcode;
        $addExtraProduct = $this->addExtraProduct;

        if (!$this->validation->isDefactoProductBarcode( $productBarcode)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это не товар дефакто'));
        }

        if($addExtraProduct == 0) {

            if (!$this->validation->isProductBarcodeExistInBox($inboundId, $clientBoxBarcode, $productBarcode)) {
                $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Этого товара нет в этом коробе'));
            }

            if ($this->validation->isExtraBarcodeInOrder($inboundId, $clientBoxBarcode, $productBarcode)) {
                $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это лишний товар в накладной'));
            }
        }
    }

    //
//    public function validateCleanOurBoxBarcode($attribute, $params)
//    {
//        $transportedBoxBarcode = $this->transportedBoxBarcode;
//        $orderNumberId = $this->orderNumberId;
//        if (!$this->validation->isFreeTransportedBoxBarcode($transportedBoxBarcode,$orderNumberId)) {
//            $this->addError($attribute, '<b>[' . $transportedBoxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Эта тара пуста или занята другим заказом'));
//        }
//    }
    //
    public function validateCloseOrder($attribute, $params)
    {

    }
    //
    public function getDTO() {
        $dto = new \stdClass();
        $dto->orderNumberId = $this->orderNumberId;
        $dto->ourBoxBarcode = BarcodeService::onlyDigital($this->ourBoxBarcode);
        $dto->clientBoxBarcode = BarcodeService::onlyDigital($this->clientBoxBarcode);
//        $dto->lotBarcode = $this->lotBarcode;
        $dto->conditionType = $this->conditionType;
        $dto->productBarcode = BarcodeService::onlyDigital($this->productBarcode);
        $dto->productQty = BarcodeService::onlyDigital($this->productQty);
        $dto->addExtraProduct = $this->addExtraProduct;
        return $dto;
    }

//    public function preparedProductModel($productModel) {
//        file_put_contents('preparedProductModel-hyundai-auto.log',$productModel."\n",FILE_APPEND);
//        $tmp = explode(' ',$productModel);
//        if(isset($tmp['1'])) {
//            $barcode = $tmp['0'];
//        } else {
//            $barcode = $productModel;
//        }
//
//        $barcode = preg_replace('/\s/','',$barcode);
//
//        file_put_contents('preparedProductModel.log',$barcode."\n",FILE_APPEND);
//        return $barcode;
//    }


    //
    public function attributeLabels()
    {
        return [
            'orderNumberId' => Yii::t('inbound/forms', 'Номер партии'),
            'clientBoxBarcode' => Yii::t('inbound/forms', 'ШК короба клиента'),
            'ourBoxBarcode' => Yii::t('inbound/forms', 'ШК нашего короба'),
            'lotBarcode' => Yii::t('inbound/forms', 'ШК лота'),
            'productBarcode' => Yii::t('inbound/forms', 'ШК товара'),
//            'productQty' => Yii::t('inbound/forms', 'Кол-во товара'),
            'conditionType' => Yii::t('inbound/forms', 'Состояние'),
        ];
    }

    //    public function validateProductModel($attribute,$params)
//    {
//        $this->productModel = $this->preparedProductModel($this->productModel);
//        $productModelBarcode = $this->productModel;
//        $orderNumberId = $this->orderNumberId;
//        if (!$this->validation->isProductModelBarcodeExistInOrder($productModelBarcode,$orderNumberId)) {
//            $this->addError($attribute, '<b>[' . $productModelBarcode . ']</b> ' . Yii::t('inbound/errors', 'Этого Артикла нет в этой накладной'));
//        }
//    }
}