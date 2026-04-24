<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\returns\domain;

use common\components\BarcodeManager;
use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceStock;
use stockDepartment\modules\kaspi\models\ProductBarcodesV2;
use Yii;
use yii\base\Model;

class ReturnScanningForm extends Model
{
    public $client_id;
    public $order_number;
    public $product_barcode;
    public $box_barcode;
    public $party_number;

    const SCENARIO_ORDER_NUMBER = 'ORDER-NUMBER';
    const SCENARIO_BOX_BARCODE = 'BOX-BARCODE';
    const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';

    public function rules()
    {
        return [
            [['order_number'], 'required', 'on' => self::SCENARIO_ORDER_NUMBER],
            [['client_id'], 'integer'],
            [['order_number', 'box_barcode', 'product_barcode', 'party_number'], 'string'],

            [['box_barcode'], 'validateBoxBarcode', 'on' => self::SCENARIO_BOX_BARCODE],
            [['box_barcode'], 'validateBoxBarcodeOnly5000', 'on' => self::SCENARIO_BOX_BARCODE],
            [['box_barcode'], 'trim', 'on' => self::SCENARIO_BOX_BARCODE],
            [['box_barcode'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],

            [['box_barcode'], 'validateBoxBarcodeOnly5000', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['product_barcode'], 'validateProductBarcode', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['product_barcode', 'box_barcode', 'order_number'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['product_barcode', 'box_barcode', 'order_number'], 'trim', 'on' => self::SCENARIO_PRODUCT_BARCODE],

            [['client_id', 'order_number'], 'required', 'on' => 'ConfirmOrder'],
            [['client_id', 'order_number', 'box_barcode'], 'required', 'on' => 'ClearBox'],
            [['box_barcode'], 'validateClearBox', 'on' => 'ClearBox'],
            [['client_id', 'order_number', 'box_barcode', 'product_barcode'], 'required', 'on' => 'ClearProductInBox'],
            [['product_barcode'], 'validateProductInBox', 'on' => 'ClearProductInBox'],
        ];
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

    public function validateBoxBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if (!BarcodeManager::isBox($value)) {
            $this->addError(
                $attribute,
                '<b>[' . $value . ']</b> ' . Yii::t('inbound/errors', 'Invalid box barcode. Box barcode first letter must be b')
            );
        }

        $returnId = $this->order_number;
        $conflictSameBox = EcommerceStock::find()
            ->andWhere([
                'box_address_barcode' => $value,
                'status' => [
                    EcommerceStock::STATUS_INBOUND_SCANNING,
                    EcommerceStock::STATUS_INBOUND_SCANNED,
                    EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                ],
            ])
            ->andWhere(['not', ['return_id' => $returnId]])
            ->andWhere(['not', ['return_id' => 0]])
            ->exists();

        if ($conflictSameBox) {
            $this->addError(
                $attribute,
                '<b>[' . $value . ']</b> ' . Yii::t('inbound/errors', 'В этом коробе есть товары из другого заказа')
            );
        }
    }

    public function validateBoxBarcodeOnly5000($attribute, $params)
    {
        $boxBarcode = $this->box_barcode;
        $inboundError = BarcodeManager::isValidInboundBoxBarcode($boxBarcode);
        if ($inboundError) {
            $this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', $inboundError));
        }
    }

    public function validateClearBox($attribute, $params)
    {
        $value = $this->$attribute;
        if (EcommerceReturn::find()->andWhere(['status' => ReturnOutboundStatus::DONE, 'id' => $this->order_number])->exists()) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> ' . Yii::t('inbound/errors', 'This order is complete'));
        }
    }

    public function validateProductInBox($attribute, $params)
    {
        $productBarcode = $this->$attribute;
        $box_barcode = $this->box_barcode;

        if (!self::checkProductInBox($productBarcode, $box_barcode)) {
            $this->addError($attribute, '<b> [ ' . $productBarcode . ' ] </b> ' . Yii::t('inbound/errors', 'Короб пуст'));
        }
        if (EcommerceReturn::find()->andWhere(['status' => ReturnOutboundStatus::DONE, 'id' => $this->order_number])->exists()) {
            $this->addError($attribute, '<b> [ ' . $productBarcode . ' ] </b> ' . Yii::t('inbound/errors', 'This order is complete'));
        }
    }

    public function validateProductBarcode($attribute, $params)
    {
        $productBarcode = $this->$attribute;
        if (!ProductBarcodesV2::find()->andWhere(['barcode' => $productBarcode])->exists()) {
            $this->addError($attribute, '<b> [ ' . $productBarcode . ' ] </b> ' . Yii::t('inbound/errors', 'В нашей системе нет такого ШК товара'));
        }
    }

    public function checkProductInBox($productBarcode, $box_barcode)
    {
        return EcommerceStock::find()
            ->where([
                'box_address_barcode' => $box_barcode,
                'product_barcode' => $productBarcode,
                'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
            ])
            ->exists();
    }
}
