<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\inbound\domain;
use Yii;
use yii\base\Model;
use common\components\BarcodeManager;
use common\modules\stock\models\Stock;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\clientObject\main\inbound\validation\InboundOrderValidation;

class InboundForm extends Model
{
    public $client_id;
    public $order_number;
    public $product_barcode;
    public $box_barcode;
    public $party_number;
    private $validation;
    private $inboundReturnService;

	const SCENARIO_ORDER_NUMBER = 'ORDER-NUMBER';
	const SCENARIO_BOX_BARCODE = 'BOX-BARCODE';
	const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->validation = new InboundOrderValidation();
        $this->inboundReturnService = new InboundReturnService();
    }

    public function rules()
    {
        return [
            [['order_number'], 'required', 'on' => self::SCENARIO_ORDER_NUMBER],
            [['client_id'], 'integer'],
            [['order_number', 'box_barcode', 'product_barcode', 'party_number'], 'string'],
            [['box_barcode'], 'validateBoxBarcode', 'on' =>  self::SCENARIO_BOX_BARCODE],
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

    /*
     * Validate box_barcode
     * */
    public function validateBoxBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if (!BarcodeManager::isBox($value)) {
            $this->addError(
                $attribute,
                '<b>[' . $value . ']</b> ' . Yii::t(
                    'inbound/errors',
                    'Invalid box barcode. Box barcode first letter must be b'
                )
            );
        }

        $inbound_order_id = $this->order_number;
        $count = Stock::find()
            ->andWhere([
                'primary_address' => $value,
                'status' => [
                    Stock::STATUS_INBOUND_SCANNING,
                    Stock::STATUS_INBOUND_SCANNED,
                    Stock::STATUS_INBOUND_OVER_SCANNED
                ]
            ])
            ->andWhere(
                'inbound_order_id != :inbound_order_id',
                [':inbound_order_id' => $inbound_order_id]
            )->exists();

        if ($count) {
            $this->addError(
                $attribute,
                '<b>[' . $value . ']</b> ' . Yii::t(
                    'inbound/errors',
                    'В этом коробе есть товары из другого заказа'
                )
            );
        }

        $count = Stock::find()
            ->andWhere(
                [
                    'primary_address' => $value,
                ]
            )
            ->andWhere(
                'inbound_order_id != :inbound_order_id AND secondary_address != ""',
                [':inbound_order_id' => $inbound_order_id]
            )->exists();

        if ($count) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('inbound/errors', 'В этом коробе есть товары из другого заказа и он уже размещен'));
        }
    }

    /**
     * Validate box_barcode
     * */
    public function validateBoxBarcodeOnly5000($attribute, $params)
    {
        $boxBarcode = $this->box_barcode;
        $inboundError = BarcodeManager::isValidInboundBoxBarcode($boxBarcode);
        if ($inboundError) {
            $this->addError(
                $attribute,
                '<b>[' . $boxBarcode . ']</b> ' . Yii::t(
                    'outbound/errors',
                    $inboundError
                )
            );
        }
    }

    /*
     * Remove all product in box
     *
     * */
    public function validateClearBox($attribute, $params)
    {
        $value = $this->$attribute;

        if (
            InboundOrder::find()
                ->andWhere(
                    [
                        'status' => Stock::STATUS_INBOUND_COMPLETE,
                        'id' => $this->order_number
                    ]
                )->exists()
        ) {
            $this->addError(
                $attribute,
                '<b> [ ' . $value . ' ] </b> ' . Yii::t(
                    'inbound/errors',
                    'This order is complete'
                )
            );
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
        if (!self::checkProductInBox($productBarcode, $box_barcode)) {
            $this->addError(
                $attribute,
                '<b> [ ' . $productBarcode . ' ] </b> ' . Yii::t(
                    'inbound/errors',
                    'Короб пуст'
                )
            );
        }
        if (
            InboundOrder::find()
                ->andWhere(
                    [
                        'status' => Stock::STATUS_INBOUND_COMPLETE,
                        'id' => $this->order_number
                    ]
                )
                ->exists()
        ) {
            $this->addError(
                $attribute,
                '<b> [ ' . $productBarcode . ' ] </b> ' . Yii::t(
                    'inbound/errors',
                    'This order is complete'
                )
            );
        }
    }

    /**
     * Validate product_barcode
     * */
    public function validateProductBarcode($attribute, $params)
    {
        $productBarcode = $this->$attribute;
        if (!$this->inboundReturnService->isValidProductBarcode($productBarcode)) {
            $this->addError(
                $attribute,
                '<b> [ ' . $productBarcode . ' ] </b> ' . Yii::t(
                    'inbound/errors',
                    'В нашей системе нет такого ШК товара'
                )
            );
        }
    }

    /*
     * Check exist product in box
     * @param string $productBarcode
     * @return boolean
     * */
    public function checkProductInBox($productBarcode, $box_barcode)
    {
        return Stock::find()
            ->where(
                [
                    'box_barcode' => $box_barcode,
                    'product_barcode' => $productBarcode,
                    'status' => Stock::STATUS_OUTBOUND_SCANNED
                ]
            )
            ->exists();
    }
}