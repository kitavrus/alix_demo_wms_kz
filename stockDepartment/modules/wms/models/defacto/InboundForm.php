<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\wms\models\defacto;

use common\components\BarcodeManager;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
//use common\modules\inbound\models\InboundOrderItemProcess;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;

class InboundForm extends Model {

    public $client_id;
    public $order_number;
    public $product_barcode;
    public $box_barcode;
    public $client_box_barcode;
    public $party_number;
    public $withExtraLot = 0;

    private $inboundOrderModel;
    private $inboundOrderItemModel;
    private $stockModel;

    /*
     * */
    public function rules()
    {
        return [
            [['client_id','order_number','withExtraLot'], 'required'],
            [['client_id','withExtraLot'], 'integer'],
            [['order_number','box_barcode','product_barcode','party_number','client_box_barcode'], 'string'],
            [['box_barcode'], 'validateBoxBarcode'],
            [['box_barcode'], 'trim'],

            [['client_id','order_number'], 'required','on'=>'ConfirmOrder'],
            [['client_id','order_number','box_barcode'], 'required','on'=>'ClearBox'],
            [['box_barcode'], 'validateClearBox','on'=>'ClearBox'],
            [['client_id','order_number','box_barcode','product_barcode'], 'required','on'=>'ClearProductInBox'],
            [['product_barcode'], 'validateProductInBox','on'=>'ClearProductInBox'],

            [['product_barcode'], 'validateProductBarcode','on'=>'ScannedProduct'],
            [['product_barcode','box_barcode','client_box_barcode','withExtraLot'], 'required','on'=>'ScannedProduct'],

            [['client_box_barcode'], 'validateClientBoxBarcode','on'=>'ScannedProduct'],

            [['client_box_barcode'], 'validateClientBoxBarcode','on'=>'ScanningClientBoxBarcode'],
            [['client_box_barcode','order_number'], 'required','on'=>'ScanningClientBoxBarcode'],


            [['box_barcode'], 'trim','on'=>'ScannedBox'],
            [['box_barcode'], 'required','on'=>'ScannedBox'],
            [['box_barcode'], 'validateBoxBarcode','on'=>'ScannedBox'],

            [['client_box_barcode'], 'trim','on'=>'ClientScannedBox'],
            [['client_box_barcode','order_number'], 'required','on'=>'ClientScannedBox'],
            [['client_box_barcode'], 'validateClientBoxBarcode','on'=>'ClientScannedBox'],

        ];
    }

    /*
     * */
    public function attributeLabels()
    {
        return [
            'party_number' => Yii::t('inbound/forms', 'Party number'),
            'client_id' => Yii::t('inbound/forms', 'Client'),
            'order_number' => Yii::t('inbound/forms', 'Order number'),
            'box_barcode' => Yii::t('inbound/forms', 'Box barcode'),
            'client_box_barcode' => Yii::t('inbound/forms', 'Client Box barcode'),
            'product_barcode' => Yii::t('inbound/forms', 'Product barcode'),
            'withExtraLot' => Yii::t('inbound/forms', 'Принимаем плюсы'),
        ];
    }

    /*
    * Validate client box barcode
    * */
    public function validateClientBoxBarcode($attribute, $params)
    {
        $clientBox = $this->$attribute;
        if(!BarcodeManager::isDefactoBox($clientBox)) {
            $this->addError($attribute, '<b>['.$clientBox.']</b> '.Yii::t('inbound/errors','"Это не штрихкод короба Дефакто"'));
        }
    }

    /*
    * Validate box_barcode
    * */
    public function validateBoxBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isBoxOnlyOur($value)) {
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
//        $value = $this->$attribute;
//        $inbound_order_id = $this->order_number;
//        $client_box_barcode = $this->client_box_barcode;
//        if(!self::checkProductBarcode($value,$inbound_order_id,$client_box_barcode)) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Scanned product barcode not found in selected inbound').' или коробе клиента');
//        }

        $value = $this->$attribute;
        if(!BarcodeManager::isDefactoProduct($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','Этот не штрих код товара'));
        }

        if($this->withExtraLot == 0) {
            $inboundOrderID = $this->order_number;
            $productBarcode = $this->product_barcode;
            $clientBoxBarcode = $this->client_box_barcode;

            $extraQty = InboundOrderItem::find()->andWhere([
                'inbound_order_id' => $inboundOrderID,
                'product_barcode' => $productBarcode,
                'box_barcode' => $clientBoxBarcode
            ])->andWhere('expected_qty = accepted_qty AND expected_qty != 0')->exists();

            if ($extraQty) {
                $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('inbound/errors', 'Это лишний товар в накладной'));
            } else {

                $extraQty = InboundOrderItem::find()->andWhere([
                    'inbound_order_id' => $inboundOrderID,
                    'product_barcode' => $productBarcode,
                    'box_barcode' => $clientBoxBarcode
                ])->exists();

                if (!$extraQty) {
                    $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('inbound/errors', 'Этого лота нет в этом коробе'));
                }
            }
        }
		
        /*if($this->withExtraLot == 0) {
            $inboundOrderID = $this->order_number;
            $productBarcode = $this->product_barcode;
            $clientBoxBarcode = $this->client_box_barcode;

            $extraQty = InboundOrderItem::find()->andWhere([
                'inbound_order_id' => $inboundOrderID,
                'product_barcode' => $productBarcode,
                'box_barcode' => $clientBoxBarcode
            ])->andWhere('expected_qty = accepted_qty AND expected_qty != 0')->exists();

            if ($extraQty) {
                $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('inbound/errors', 'Это лишний товар в накладной'));
            }
        }*/
    }

    /*
     * Remove product in box
     * */
    public function validateProductInBox($attribute, $params)
    {
        $value = $this->$attribute;
        $box_barcode = $this->box_barcode;
        $client_box_barcode = $this->client_box_barcode;
        if(!self::checkProductInBox($value,$box_barcode)) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Короб пуст')); // Этого товара нет в укзанном коробе
        }

        if(InboundOrder::find()->andWhere(['status'=>Stock::STATUS_INBOUND_COMPLETE,'id'=> $this->order_number])->exists()) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This order is complete'));
        }
    }

    /*
     * Remove all product in box
     * */
    public function validateClearBox($attribute, $params)
    {
        $value = $this->$attribute;
        if(InboundOrder::find()->andWhere(['status'=>Stock::STATUS_INBOUND_COMPLETE,'id'=> $this->order_number])->exists()) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This order is complete'));
        }
    }

    /*
     * Проверяет существует ли отсканированный товар в выбранном заказе
     * @param string $productBarcode
     * @param integer $inbound_order_id
     * @param string $client_box_barcode
     * @return
     * */
    public function checkProductBarcode($productBarcode,$inbound_order_id,$client_box_barcode)
    {
        return Stock::find()
            ->andWhere([
                    'product_barcode'=>$productBarcode,
                    'inbound_order_id'=>$inbound_order_id,
                    'inbound_client_box'=>$client_box_barcode
                ]
            )
            ->exists();
    }

    /*
    * Check exist product in box
    * @param string $productBarcode
    * @return boolean
    * */
    public function checkProductInBox($productBarcode,$box_barcode)
    {
        return Stock::find()->andWhere([
            'primary_address'=>$box_barcode,
            'product_barcode'=>$productBarcode,
            'status'=>[
                Stock::STATUS_INBOUND_SCANNED,
                Stock::STATUS_INBOUND_OVER_SCANNED
            ]])->exists();
    }
    /*
     * Находим отсканированный шк в заказе и ставим статус отсканирован
     * */
    public function setScannedStatus()
    {
        $stock = Stock::find()->andWhere([
            'inbound_order_id' => $this->order_number,
            'product_barcode' => $this->product_barcode,
            'inbound_client_box' => $this->client_box_barcode,
            'status' => [Stock::STATUS_INBOUND_NEW,Stock::STATUS_INBOUND_SCANNING],
            'client_id' => $this->client_id,
        ])->one();

        if($stock) {
            $stock->status = Stock::STATUS_INBOUND_SCANNED;
            $stock->primary_address = $this->box_barcode;
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
                'inbound_client_box'=>$this->client_box_barcode,
                'status'=>Stock::STATUS_INBOUND_SCANNED,
                'status_availability'=>Stock::STATUS_AVAILABILITY_NO,
                'system_status'=>$this->client_id.'-'.'OVER-SCAN',
                'system_status_description'=>'Это товар которога не должно быть в этом коробе. Но по факту он есть',
            ];
            $stock->setAttributes($attributes,false);
            $stock->save(false);


            $inboundItemOne = InboundOrderItem::find()
                ->andWhere([
                    'inbound_order_id'=>$this->order_number,
                    'product_barcode'=>$this->product_barcode,
                    'box_barcode'=>$this->client_box_barcode,
                ])
                ->one();

            if(!$inboundItemOne) {
                $inboundItemOne = new InboundOrderItem();
                $inboundItemOne->inbound_order_id = $this->order_number;
                $inboundItemOne->product_barcode = $this->product_barcode;
                $inboundItemOne->box_barcode = $this->client_box_barcode;
                $inboundItemOne->save(false);
            }

            $stock->inbound_order_item_id = $inboundItemOne->id;
            $stock->save(false);

        }
        $this->setStockModel($stock);
        return $stock;
    }

    /*
     * удаляем лот из нашего короба
     *  */
    public function deleteProductFromBox()
    {
        $stock = Stock::find()->andWhere([
            'inbound_order_id' => $this->order_number,
            'product_barcode' => $this->product_barcode,
            'inbound_client_box' => $this->client_box_barcode,
            'system_status'=>$this->client_id.'-'.'OVER-SCAN',
            'client_id' => $this->client_id,
            'primary_address' => $this->box_barcode,
        ])->one();

        if($stock) {
            $stock->delete();
        } else {
            $stock = Stock::find()->andWhere([
                'inbound_order_id' => $this->order_number,
                'product_barcode' => $this->product_barcode,
                'inbound_client_box' => $this->client_box_barcode,
                'primary_address' => $this->box_barcode,
                'status'=>[
                    Stock::STATUS_INBOUND_SCANNED,
                    Stock::STATUS_INBOUND_OVER_SCANNED
                ],
                'client_id' => $this->client_id,
            ])->one();

            if($stock) {
                $stock->status = Stock::STATUS_INBOUND_NEW;
                $stock->primary_address = '';
                $stock->system_status_description = 'Used method: deleteProductFromBox';
                $stock->save(false);
            }
        }
    }

    /*
     * удаляем лот из нашего короба
     * @param $product_barcode string
     * @param $client_box_barcode string
     *  */
    public function removeAllProductsFromBox($product_barcode = null,$client_box_barcode = null, $primary_address = null)
    {
        $product_barcode = is_null($product_barcode) ? $this->product_barcode : $product_barcode;
        $client_box_barcode = is_null($client_box_barcode) ? $this->client_box_barcode : $client_box_barcode;
        $primary_address = is_null($primary_address) ? $this->box_barcode : $primary_address;

        if(!empty($this->order_number) && !empty($product_barcode) && !empty($client_box_barcode)  && !empty($primary_address) ) {

            Stock::deleteAll([
                'inbound_order_id' => $this->order_number,
                'product_barcode' => $product_barcode,
                'inbound_client_box' => $client_box_barcode,
                'system_status' => $this->client_id . '-' . 'OVER-SCAN',
                'client_id' => $this->client_id,
                'primary_address' => $primary_address,
            ]);

            $stocks = Stock::find()->andWhere([
                'inbound_order_id' => $this->order_number,
                'product_barcode' => $product_barcode,
                'inbound_client_box' => $client_box_barcode,
                'primary_address' => $primary_address,
                'client_id' => $this->client_id,
                'status' => [
                    Stock::STATUS_INBOUND_SCANNED,
                    Stock::STATUS_INBOUND_OVER_SCANNED
                ],
            ])->all();

            if($stocks) {
                foreach($stocks as $stock) {
                    $stock->status = Stock::STATUS_INBOUND_NEW;
                    $stock->primary_address = '';
                    $stock->save(false);
                }
            }
        }
    }

    /*
     * Обновляем количество и статус в inbound order item
     * */
    public function updateInboundOrderItem()
    {
        $countStockForItem =  Stock::find()->andWhere([
            'inbound_order_id' => $this->order_number,
            'product_barcode' => $this->product_barcode,
            'inbound_client_box'=>$this->client_box_barcode,
            'status' => [Stock::STATUS_INBOUND_SCANNED,Stock::STATUS_INBOUND_OVER_SCANNED],
            'client_id' => $this->client_id,
        ])->count();

        $inboundOrderItem = InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $this->order_number,
            'product_barcode' => $this->product_barcode,
            'box_barcode'=>$this->client_box_barcode,
        ])->one();

        if ($inboundOrderItem) {

            if(intval($inboundOrderItem->accepted_qty) < 1) {
                $inboundOrderItem->begin_datetime = time();
                $inboundOrderItem->status = Stock::STATUS_INBOUND_NEW;
            }

            $inboundOrderItem->accepted_qty = $countStockForItem;

            if($inboundOrderItem->accepted_qty == $inboundOrderItem->expected_qty) {
                $inboundOrderItem->status = Stock::STATUS_INBOUND_SCANNED;
            }

            $inboundOrderItem->end_datetime = time();
            $inboundOrderItem->save(false);
        }
        $this->setInboundOrderItemModel($inboundOrderItem);
        return $inboundOrderItem;
    }

    /*
     * Обновляем количество и статус в inbound order item
     * */
    public function updateInboundOrderItems()
    {
        $dataScannedProductByBarcode = [];

        $productsInBox = Stock::find()
                            ->select('count(product_barcode) as product_barcode_count, product_barcode, inbound_client_box')
                            ->andWhere([
                                'primary_address' => $this->box_barcode,
                                'inbound_order_id' => $this->order_number,
                                'status' => [
                                    Stock::STATUS_INBOUND_SCANNED
                                ]])
                            ->groupBy('product_barcode, inbound_client_box')
                            ->all();
        if ($productsInBox) {

            foreach ($productsInBox as $stock) {
                $inboundOrderItem = InboundOrderItem::findOne([
                        'product_barcode' => $stock->product_barcode,
                        'inbound_order_id' => $this->order_number,
                        'box_barcode' =>   $stock->inbound_client_box
                    ]
                );

                if ($inboundOrderItem) {

                    $this->removeAllProductsFromBox($stock->product_barcode,$stock->inbound_client_box);
                    $inboundOrderItem->accepted_qty = $this->getScannedProductInItem($stock->inbound_client_box,$stock->product_barcode);
                    $inboundOrderItem->save(false);

                    $colorRowClass = 'alert-danger';
                    if ($inboundOrderItem->accepted_qty == $inboundOrderItem->expected_qty) {
                        $colorRowClass = 'alert-success';
                    } elseif ($inboundOrderItem->accepted_qty > $inboundOrderItem->expected_qty) {
                        $colorRowClass = 'alert-warning';
                    }

                    $countValue = $inboundOrderItem->accepted_qty;
                    $rowId = $inboundOrderItem->id . '-' . $stock->product_barcode;

                    $dataScannedProductByBarcode [] = [
                        'rowId' => $rowId,
                        'countValue' => $countValue,
                        'colorRowClass' => $colorRowClass
                    ];
                };
            }
        }

        return $dataScannedProductByBarcode;
    }

    /*
     * */
    public function updateInboundOrder()
    {
        if($inboundModel = InboundOrder::findOne($this->order_number)) {

            if(intval($inboundModel->accepted_qty) < 1) {
                $inboundModel->begin_datetime = time();
                $inboundModel->status = Stock::STATUS_INBOUND_SCANNING;
            }

            $inboundModel->accepted_qty = $this->getAllScannedProductInOrder();

            if( $inboundModel->accepted_qty == $inboundModel->expected_qty) {
                $inboundModel->status = Stock::STATUS_INBOUND_SCANNED;
            }

            $inboundModel->end_datetime = time();
            $inboundModel->save(false);
        }
        $this->setInboundOrderModel($inboundModel);
        return $inboundModel;
    }

    /*
     *
     * */
    public function updateConsignmentInboundOrders()
    {
        $inboundModel = $this->getInboundOrderModel();
        $coi = ConsignmentInboundOrders::findOne($inboundModel->consignment_inbound_order_id);
        if($coi) {

            $inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inboundModel->consignment_inbound_order_id])->asArray()->column();

            $coi->accepted_qty = Stock::find()->where([
                                    'inbound_order_id' => $inboundIDs,
                                    'status' => [
                                            Stock::STATUS_INBOUND_SCANNED,
                                            Stock::STATUS_INBOUND_OVER_SCANNED,
                                        ],
                                    'client_id' => $this->client_id,
                                ])->count();

            $coi->save(false);
        }
        return $coi;
    }

    /*
     *
     * */
    public function getAllScannedProductInOrder()
    {
        return Stock::find()->andWhere([
            'inbound_order_id' => $this->order_number,
            'status' => [
                Stock::STATUS_INBOUND_SCANNED,
                Stock::STATUS_INBOUND_OVER_SCANNED,
            ],
            'client_id' => $this->client_id,
        ])->count();
    }

    /*
    * Get count product in box
    * @param string $boxBarcode
    * @return integer
    *
    * */
    public function getScannedProductInBox()
    {
        return (int)Stock::find()->andWhere([
            'inbound_order_id'=>$this->order_number,
            'primary_address' => $this->box_barcode,
            'status'=>
                [
                    Stock::STATUS_INBOUND_SCANNED,
                    Stock::STATUS_INBOUND_OVER_SCANNED,
                ]])->count();

    }

    /*
    * Get count product in box
    * @param string $clientBoxBarcode
    * @return integer
    *
    * */
    public function getScannedProductInItem($clientBoxBarcode,$product_barcode)
    {
        return (int)Stock::find()->andWhere([
            'inbound_order_id'=>$this->order_number,
//            'primary_address' => $this->box_barcode,
            'inbound_client_box' => $clientBoxBarcode,
            'product_barcode' => $product_barcode,
            'status'=>
                [
                    Stock::STATUS_INBOUND_SCANNED,
                    Stock::STATUS_INBOUND_OVER_SCANNED,
                ]])->count();
    }

    /*
     * */
    public function getInboundOrderModel() {
        return $this->inboundOrderModel;
    }

    /*
     * */
    public function setInboundOrderModel($m)
    {
        return $this->inboundOrderModel = $m;
    }

    /*
     * */
    public function getInboundOrderItemModel()
    {
        return $this->inboundOrderItemModel;
    }

    /*
     * */
    public function setInboundOrderItemModel($m)
    {
        return $this->inboundOrderItemModel = $m;
    }

    /*
     * */
    public function getStockModel()
    {
        return $this->stockModel;
    }

    /*
     * */
    public function setStockModel($m)
    {
        return $this->stockModel = $m;
    }

    public function getLotInfoInClientBox($clientBoxBarcode = null)
    {
        if(is_null($clientBoxBarcode)) {
            $clientBoxBarcode = $this->client_box_barcode;
        }

        $qtyLotInClientBox = -1;
        $lotBarcodeInClientBox = -1;
        $returnData = [
            'qtyLotInClientBox' => $qtyLotInClientBox,
            'lotBarcodeInClientBox' => $lotBarcodeInClientBox,
            'statusAccepted' => 'n',
        ];

        if(empty($clientBoxBarcode)) {
            return $returnData;
        }

        $qtyLotInClientBox = InboundOrderItem::find()
                        ->andWhere(['inbound_order_id'=>$this->order_number])
                        ->andWhere(['box_barcode'=>$clientBoxBarcode])
                        ->andWhere("expected_qty > 0 AND expected_number_places_qty > 0")
                        ->sum('expected_qty');

        $inboundOrder = InboundOrderItem::find()
                        ->andWhere(['inbound_order_id'=>$this->order_number])
                        ->andWhere(['box_barcode'=>$clientBoxBarcode])
                        ->andWhere("expected_qty > 0 AND expected_number_places_qty > 0")
                        ->one();

        if($inboundOrder) {
//            $returnData['qtyLotInClientBox'] = $qtyLotInClientBox;
            $returnData['qtyLotInClientBox'] = $qtyLotInClientBox / $inboundOrder->expected_number_places_qty;
            $returnData['lotBarcodeInClientBox'] = $inboundOrder->product_barcode;
            if($returnData['qtyLotInClientBox'] == 1) {
                $returnData['statusAccepted'] = 'y';
            }
        }

        return $returnData;
    }
}