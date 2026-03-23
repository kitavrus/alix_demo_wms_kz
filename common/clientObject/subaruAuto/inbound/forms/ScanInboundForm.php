<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */

namespace common\clientObject\subaruAuto\inbound\forms;

use common\clientObject\subaruAuto\inbound\validation\InboundOrderValidation;
use Yii;
use yii\base\Model;

class ScanInboundForm extends Model
{
    private $validation;

    public $orderNumberId;
//    public $productModel; // Это артикул у клиента
    public $productBarcode;
    public $transportedBoxBarcode;
    public $productQty;
    public $conditionType;
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
            [['orderNumberId'], 'required', 'on' => 'onOrderNumber'],
            [['orderNumberId'], 'integer', 'on' => 'onOrderNumber'],
            // Scan transporter box
            [['transportedBoxBarcode', 'orderNumberId'], 'required', 'on' => 'onTransportedBoxBarcode'],
            [['transportedBoxBarcode'], 'string', 'on' => 'onTransportedBoxBarcode'],
            [['transportedBoxBarcode'], 'trim', 'on' => 'onTransportedBoxBarcode'],
            [['transportedBoxBarcode'], 'validateTransportedBox', 'on' => 'onTransportedBoxBarcode'],
            // Scan Article (model)
//            [['productModel','orderNumberId'], 'required', 'on' => 'onProductModel'],
//            [['productModel'], 'string', 'on' => 'onProductModel'],
//            [['productModel'], 'trim', 'on' => 'onProductModel'],
//            [['productModel'], 'validateProductModel', 'on' => 'onProductModel'],
            // Scan product
            [['productBarcode', 'transportedBoxBarcode', 'orderNumberId','conditionType'], 'required', 'on' => 'onProductBarcode'],
            [['productBarcode'], 'string', 'on' => 'onProductBarcode'],
            [['productBarcode'], 'trim', 'on' => 'onProductBarcode'],
            [['productBarcode'], 'validateProduct', 'on' => 'onProductBarcode'],
            // Set product qty
            [['productQty','productBarcode', 'transportedBoxBarcode', 'orderNumberId','conditionType'], 'required', 'on' => 'onProductQty'],
            [['productQty'], 'integer', 'min'=>1,'max'=>2000, 'on' => 'onProductQty'],
            [['productQty'], 'trim', 'on' => 'onProductQty'],
            [['productQty'], 'validateProductQty', 'on' => 'onProductQty'],
            // Clean transporter box
            [['transportedBoxBarcode', 'orderNumberId'], 'required', 'on' => 'onCleanTransportedBox'],
            [['transportedBoxBarcode'], 'string', 'on' => 'onCleanTransportedBox'],
            [['transportedBoxBarcode'], 'trim', 'on' => 'onCleanTransportedBox'],
            [['transportedBoxBarcode'], 'validateCleanTransportedBox', 'on' => 'onCleanTransportedBox'],
            // Show order items
            [['orderNumberId'], 'required', 'on' => 'onShowOrderItems'],
            // Print diff in order
            [['orderNumberId'], 'required', 'on' => 'onPrintDiffInOrder'],
            // Close order
            [['orderNumberId'], 'required', 'on' => 'onCloseOrder'],
            [['orderNumberId'], 'string', 'on' => 'onCloseOrder'],
            [['orderNumberId'], 'trim', 'on' => 'onCloseOrder'],
            [['orderNumberId'], 'validateCloseOrder', 'on' => 'onCloseOrder'],
        ];
    }
    //

    //
    public function validateTransportedBox($attribute, $params)
    {
        $transportedBoxBarcode = $this->transportedBoxBarcode;
        $orderNumberId = $this->orderNumberId;

        if($this->validation->isTransportedBoxBarcode($transportedBoxBarcode)) {
            if (!$this->validation->isFreeTransportedBoxBarcode($transportedBoxBarcode, $orderNumberId)) {
                $this->addError($attribute, '<b>[' . $transportedBoxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Эта транспортная тара занята или несуществует'));
            }
        }

        if(!$this->validation->isTransportedBoxBarcode($transportedBoxBarcode) && !$this->validation->isInboundUnitAddress($transportedBoxBarcode)) {
            $this->addError($attribute, '<b>[' . $transportedBoxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это не транспортная тара или не короб для размещения'));
        }
    }
    //
    public function validateProduct($attribute, $params)
    {
        $this->productBarcode = $this->preparedProductModel($this->productBarcode);
//        $this->productModel = $this->preparedProductModel($this->productModel);
        $productBarcode = $this->productBarcode;
//        $productModel = $this->productModel;
        $orderNumberId = $this->orderNumberId;

//        if(!$this->validation->isEmptyProductBarcodeByModel($productModel)) {
        if (!$this->validation->isProductBarcodeExistInOrder($productBarcode,$orderNumberId)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Этого товара нет в этой накладной'));
        }

//            if(!$this->validation->isProductDiffModel($productBarcode,$productModel)) {
//                $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Этого шк товара, другой артикул'));
//            }
//        }

        if ($this->validation->isExtraBarcodeInOrder($productBarcode,$orderNumberId)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это лишний товар в накладной'));
        }
    }

    //
    public function validateProductQty($attribute, $params)
    {
        $this->productBarcode = $this->preparedProductModel($this->productBarcode);
        $productBarcode = $this->productBarcode;
        $orderNumberId = $this->orderNumberId;
        $productQty = $this->productQty;

        if (!$this->validation->isPlusQtyBarcodeInOrder($productBarcode,$orderNumberId,$productQty)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('inbound/errors', 'Вы ввели кол-во больше чем ожидается в заказе'));
        }
    }


    //
    public function validateCleanTransportedBox($attribute, $params)
    {
        $transportedBoxBarcode = $this->transportedBoxBarcode;
        $orderNumberId = $this->orderNumberId;
        if (!$this->validation->isFreeTransportedBoxBarcode($transportedBoxBarcode,$orderNumberId)) {
            $this->addError($attribute, '<b>[' . $transportedBoxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Эта тара пуста или занята другим заказом'));
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
        $dto->transportedBoxBarcode = $this->transportedBoxBarcode;
        $dto->conditionType = $this->conditionType;
//        $dto->productModel = $this->preparedProductModel($this->productModel);
        $dto->productBarcode = $this->preparedProductModel($this->productBarcode);
        $dto->productQty = $this->productQty;
//        $dto->clientId = 0;
        return $dto;
    }

    public function preparedProductModel($productModel) {
        file_put_contents('preparedProductModel-subaru-auto.log',$productModel."\n",FILE_APPEND);
        $tmp = explode(' ',$productModel);
        if(isset($tmp['1'])) {
            $barcode = $tmp['0'];
        } else {
            $barcode = $productModel;
        }

        $barcode = preg_replace('/\s/','',$barcode);

        file_put_contents('preparedProductModel.log',$barcode."\n",FILE_APPEND);
        return $barcode;
    }


    //
    public function attributeLabels()
    {
        return [
            'orderNumberId' => Yii::t('inbound/forms', 'Номер партии'),
            'transportedBoxBarcode' => Yii::t('inbound/forms', 'ШК транспортной тары'),
//            'productModel' => Yii::t('inbound/forms', 'Артикул товара'),
            'productBarcode' => Yii::t('inbound/forms', 'ШК товара'),
            'productQty' => Yii::t('inbound/forms', 'Кол-во товара'),
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