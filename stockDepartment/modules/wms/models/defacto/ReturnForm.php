<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\wms\models\defacto;

use common\components\BarcodeManager;
use common\modules\client\models\Client;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
//use common\modules\inbound\models\InboundOrderItemProcess;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItemProduct;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

//use common\modules\codebook\models\Codebook;
//use common\modules\product\models\ProductBarcodes;

class ReturnForm extends Model {

    public $client_id;
    public $order_number;
    public $product_barcode;
    public $box_barcode;
    public $client_box_barcode;
    public $party_number;
    public $partyNumberValue;
    public $inboundOrderModel;
    public $inboundOrderItemModel;
    public $stockModel;
    public $returnItemModel;

    /*
     * */
    public function rules()
    {
        return [
            [['client_id'], 'integer'],
            [['box_barcode','party_number','client_box_barcode'], 'string'],
            // VALIDATE-OUR-BOX-BARCODE
            [['box_barcode'], 'validateOurBoxBarcode','on'=>'VALIDATE-OUR-BOX-BARCODE'],
            // VALIDATE-CLIENT-BOX-BARCODE
            [['client_box_barcode','party_number','client_id'], 'required','on'=>'VALIDATE-CLIENT-BOX-BARCODE'],
            [['client_box_barcode'], 'validateClientBoxBarcode','on'=>'VALIDATE-CLIENT-BOX-BARCODE'],
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
        ];
    }

    /*
     *
     * */
    public function getPartyNew() // ok
    {
        $ro = ReturnOrder::find()->select('id, order_number')->andWhere([
            'client_id'=>Client::CLIENT_DEFACTO,
            'status'=>[ReturnOrder::STATUS_NEW,ReturnOrder::STATUS_IN_PROCESS]
        ])->asArray()->all();

        return ArrayHelper::map($ro,'id','order_number');
    }

    /*
     * @param integer $id  Return order
     * */
    public static function getCountBoxesInParty($id) // ok
    {
        return (int)ReturnOrderItems::find()->andWhere(['return_order_id'=>$id])->count();
    }

    /*
    * Validate box_barcode
    * */
    public function validateOurBoxBarcode($attribute, $params) // ok
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isBox($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','Invalid box barcode. Box barcode first letter must be b'));
        }
    }

    /*
    * Validate client box barcode
    * */
    public function validateClientBoxBarcode($attribute, $params) // ok
    {
        $clientBoxBarcode = $this->client_box_barcode;
        $returnOrderId = $this->party_number;
        $ext = ReturnOrderItems::find()
            ->andWhere([
                'return_order_id'=>$returnOrderId,
                'client_box_barcode'=>$clientBoxBarcode,
                'status'=>ReturnOrder::STATUS_NEW,
            ])
            ->exists();

        if(!$ext) {
            $this->addError($attribute, '<b>['.$clientBoxBarcode.']</b> '.Yii::t('inbound/errors','В этом возврате короб с так штрихкодом не найдет'));
        }
    }

    /*
    * Находим отсканированный шк в заказе и ставим статус отсканирован
    * */
    public function setScannedStatus()
    {
        $returnItem = ReturnOrderItems::find()->andWhere([
                    'return_order_id' => $this->party_number,
                    'client_box_barcode' => $this->client_box_barcode,
                    'status' => [
                        ReturnOrder::STATUS_NEW
                    ],
        ])->one();

        if($returnItem) {
            $returnItem->status = ReturnOrder::STATUS_SCANNED;
            $returnItem->box_barcode = $this->box_barcode;
            $returnItem->save(false);
            $this->setReturnItemModel($returnItem);
        } else { // TODO ?!!!!
//            $stock = new Stock();
//            $attributes = [
//                'client_id'=>$this->client_id,
//                'inbound_order_id'=>$this->order_number,
//                'product_barcode'=>$this->product_barcode,
//                'primary_address'=>$this->box_barcode,
//                'inbound_client_box'=>$this->client_box_barcode,
//                'status'=>Stock::STATUS_INBOUND_SCANNED,
//                'system_status'=>$this->client_id.'-'.'OVER-SCAN',
//                'system_status_description'=>'Это товар которога не должно быть в этом коробе. Но по факту он есть',
//            ];
//            $stock->setAttributes($attributes,false);
//            $stock->save(false);
        }
//        $this->setStockModel($stock);
        return $returnItem;
    }

     /*
     *
     * */
     public function createOrUpdateInboundOrder() {

         $partyNumberValue =  $this->getPartyNumberValue();
         $inbound = InboundOrder::find()->andWhere([
             'parent_order_number'=>$this->client_box_barcode,
             'order_number'=>$partyNumberValue->order_number,
             'client_id'=>Client::CLIENT_DEFACTO,
         ])->one();

         if(!$inbound) {
             $inbound = new InboundOrder();
             $inbound->parent_order_number =  $partyNumberValue->order_number;
             $inbound->order_number = $this->client_box_barcode;
             $inbound->status = Stock::STATUS_INBOUND_SCANNED;
             $inbound->order_type = InboundOrder::ORDER_TYPE_RETURN;
             $inbound->client_id = Client::CLIENT_DEFACTO;
             $inbound->expected_qty = 0;
             $inbound->accepted_qty = 0;
             $inbound->save(false);
         }

       return $this->setInboundOrderModel($inbound);
     }

     public function createInboundOrderItemAndStock()
     {
         if($returnItemModel = $this->getReturnItemModel()) {
            $returnOrderItemProducts =  ReturnOrderItemProduct::find()->andWhere(['return_order_item_id'=>$returnItemModel->id])->all();
             if($returnOrderItemProducts) {
                 $expectedQty = 0;
                 foreach ($returnOrderItemProducts as $returnOrderItemProduct) {
                     $inboundOrderItem = new InboundOrderItem();
                     $inboundOrderItem->inbound_order_id = $this->getInboundOrderModel()->id; //TODO !
                     $inboundOrderItem->product_barcode = $returnOrderItemProduct->product_barcode;
                     $inboundOrderItem->product_serialize_data = $returnOrderItemProduct->product_serialize_data;
                     $inboundOrderItem->box_barcode = $returnOrderItemProduct->client_box_barcode;
                     $inboundOrderItem->expected_qty = $returnOrderItemProduct->expected_qty;
                     $inboundOrderItem->accepted_qty = $returnOrderItemProduct->expected_qty;
                     $inboundOrderItem->status = Stock::STATUS_INBOUND_SCANNED;
                     $inboundOrderItem->save(false);

                     $expectedQty += $inboundOrderItem->expected_qty;
                     for($i=1;$i <= $inboundOrderItem->expected_qty;++$i) {
                         $stock = new Stock();
                         $stock->client_id =  Client::CLIENT_DEFACTO;;
                         $stock->inbound_order_id = $inboundOrderItem->inbound_order_id;
                         $stock->inbound_order_item_id = $inboundOrderItem->id;
                         $stock->product_barcode = $inboundOrderItem->product_barcode;
                         $stock->product_model = '';
                         $stock->status = Stock::STATUS_INBOUND_SCANNED;
                         $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
                         $stock->inbound_client_box = $inboundOrderItem->box_barcode;
                         $stock->primary_address = $this->box_barcode;
                         $stock->save(false);
                     }
                 }
                 $inboundOrderModel = $this->getInboundOrderModel();
                 $inboundOrderModel->expected_qty = $expectedQty;
                 $inboundOrderModel->accepted_qty = $expectedQty;
                 $inboundOrderModel->from_point_id = $returnItemModel->from_point_id;
                 $inboundOrderModel->from_point_title = $returnItemModel->from_point_client_id;
                 $inboundOrderModel->to_point_id = $returnItemModel->to_point_id;
                 $inboundOrderModel->to_point_title = $returnItemModel->to_point_client_id;
                 $inboundOrderModel->save(false);
             }
         }
     }

    /*
    *
    * */
    public function getAllScannedBoxesInParty()
    {
        return ReturnOrderItems::find()->andWhere([
            'return_order_id' => $this->party_number,
            'status' => [
                ReturnOrder::STATUS_SCANNED,
                ReturnOrder::STATUS_SCANNED_OVER,
            ],
        ])->count();
    }

    /*
     *
     * */
    public function getPartyNumberValue()
    {
        return !empty($this->partyNumberValue) ? $this->partyNumberValue : $this->setPartyNumberValue();
    }

    /*
     * */
    public function setPartyNumberValue()
    {
        return $this->partyNumberValue = ReturnOrder::findOne($this->party_number);
    }

    /*
     *
     * */
    public function getInboundOrderModel()
    { // TODO Тут возможна ошибка!!!
        return $this->inboundOrderModel;
    }

    /*
     * */
    public function setInboundOrderModel($inboundOrderModel)
    {
        return $this->inboundOrderModel = $inboundOrderModel;
    }

    /*
     *
     * */
    public function getReturnItemModel()
    { // TODO Тут возможна ошибка!!!
        return $this->returnItemModel;
    }

    /*
     * */
    public function setReturnItemModel($returnItemModel)
    {
        return $this->returnItemModel = $returnItemModel;
    }


    /*
     * Validate product_barcode
     * */
//    public function validateProductBarcode($attribute, $params)
//    {
//        $value = $this->$attribute;
//        $inbound_order_id = $this->order_number;
//        $client_box_barcode = $this->client_box_barcode;
//        if(!self::checkProductBarcode($value,$inbound_order_id,$client_box_barcode)) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Scanned product barcode not found in selected inbound').' или коробе клиента');
//        }
//    }

    /*
     * Remove product in box
     * */
//    public function validateProductInBox($attribute, $params)
//    {
//        $value = $this->$attribute;
//        $box_barcode = $this->box_barcode;
//        $client_box_barcode = $this->client_box_barcode;
//        if(!self::checkProductInBox($value,$box_barcode)) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Короб пуст')); // Этого товара нет в укзанном коробе
//        }
//
//        if(InboundOrder::find()->andWhere(['status'=>Stock::STATUS_INBOUND_COMPLETE,'id'=> $this->order_number])->exists()) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This order is complete'));
//        }
//    }

    /*
     * Remove all product in box
     * */
//    public function validateClearBox($attribute, $params)
//    {
//        $value = $this->$attribute;
//        if(InboundOrder::find()->andWhere(['status'=>Stock::STATUS_INBOUND_COMPLETE,'id'=> $this->order_number])->exists()) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This order is complete'));
//        }
//    }

    /*
     * Проверяет существует ли отсканированный товар в выбранном заказе
     * @param string $productBarcode
     * @param integer $inbound_order_id
     * @param string $client_box_barcode
     * @return
     * */
//    public function checkProductBarcode($productBarcode,$inbound_order_id,$client_box_barcode)
//    {
//        return Stock::find()
//            ->andWhere([
//                    'product_barcode'=>$productBarcode,
//                    'inbound_order_id'=>$inbound_order_id,
//                    'inbound_client_box'=>$client_box_barcode
//                ]
//            )
//            ->exists();
//    }

    /*
    * Check exist product in box
    * @param string $productBarcode
    * @return boolean
    * */
//    public function checkProductInBox($productBarcode,$box_barcode)
//    {
//        return Stock::find()->andWhere([
//            'primary_address'=>$box_barcode,
//            'product_barcode'=>$productBarcode,
//            'status'=>[
//                Stock::STATUS_INBOUND_SCANNED,
//                Stock::STATUS_INBOUND_OVER_SCANNED
//            ]])->exists();
//    }
    /*
     * Находим отсканированный шк в заказе и ставим статус отсканирован
     * */
//    public function setScannedStatus()
//    {
//        $stock = Stock::find()->andWhere([
//            'inbound_order_id' => $this->order_number,
//            'product_barcode' => $this->product_barcode,
//            'inbound_client_box' => $this->client_box_barcode,
//            'status' => [Stock::STATUS_INBOUND_NEW,Stock::STATUS_INBOUND_SCANNING],
//            'client_id' => $this->client_id,
//        ])->one();
//
//        if($stock) {
//            $stock->status = Stock::STATUS_INBOUND_SCANNED;
//            $stock->primary_address = $this->box_barcode;
//            $stock->save(false);
//        } else {
//            $stock = new Stock();
//            $attributes = [
//                'client_id'=>$this->client_id,
//                'inbound_order_id'=>$this->order_number,
//                'product_barcode'=>$this->product_barcode,
//                'primary_address'=>$this->box_barcode,
//                'inbound_client_box'=>$this->client_box_barcode,
//                'status'=>Stock::STATUS_INBOUND_SCANNED,
//                'system_status'=>$this->client_id.'-'.'OVER-SCAN',
//                'system_status_description'=>'Это товар которога не должно быть в этом коробе. Но по факту он есть',
//            ];
//            $stock->setAttributes($attributes,false);
//            $stock->save(false);
//        }
//        $this->setStockModel($stock);
//        return $stock;
//    }

    /*
     * удаляем лот из нашего короба
     *  */
//    public function deleteProductFromBox()
//    {
//        $stock = Stock::find()->andWhere([
//            'inbound_order_id' => $this->order_number,
//            'product_barcode' => $this->product_barcode,
//            'inbound_client_box' => $this->client_box_barcode,
//            'system_status'=>$this->client_id.'-'.'OVER-SCAN',
//            'client_id' => $this->client_id,
//            'primary_address' => $this->box_barcode,
//        ])->one();
//
//        if($stock) {
//            $stock->delete();
//        } else {
//            $stock = Stock::find()->andWhere([
//                'inbound_order_id' => $this->order_number,
//                'product_barcode' => $this->product_barcode,
//                'inbound_client_box' => $this->client_box_barcode,
//                'primary_address' => $this->box_barcode,
//                'status'=>[
//                    Stock::STATUS_INBOUND_SCANNED,
//                    Stock::STATUS_INBOUND_OVER_SCANNED
//                ],
//                'client_id' => $this->client_id,
//            ])->one();
//
//            if($stock) {
//                $stock->status = Stock::STATUS_INBOUND_NEW;
//                $stock->primary_address = '';
//                $stock->system_status_description = 'Used method: deleteProductFromBox';
//                $stock->save(false);
//            }
//        }
//    }

    /*
     * удаляем лот из нашего короба
     * @param $product_barcode string
     * @param $client_box_barcode string
     *  */
//    public function removeAllProductsFromBox($product_barcode = null,$client_box_barcode = null, $primary_address = null)
//    {
//        $product_barcode = is_null($product_barcode) ? $this->product_barcode : $product_barcode;
//        $client_box_barcode = is_null($client_box_barcode) ? $this->client_box_barcode : $client_box_barcode;
//        $primary_address = is_null($primary_address) ? $this->box_barcode : $primary_address;
//
//        if(!empty($this->order_number) && !empty($product_barcode) && !empty($client_box_barcode)  && !empty($primary_address) ) {
//
//            Stock::deleteAll([
//                'inbound_order_id' => $this->order_number,
//                'product_barcode' => $product_barcode,
//                'inbound_client_box' => $client_box_barcode,
//                'system_status' => $this->client_id . '-' . 'OVER-SCAN',
//                'client_id' => $this->client_id,
//                'primary_address' => $primary_address,
//            ]);
//
//            $stocks = Stock::find()->andWhere([
//                'inbound_order_id' => $this->order_number,
//                'product_barcode' => $product_barcode,
//                'inbound_client_box' => $client_box_barcode,
//                'primary_address' => $primary_address,
//                'client_id' => $this->client_id,
//                'status' => [
//                    Stock::STATUS_INBOUND_SCANNED,
//                    Stock::STATUS_INBOUND_OVER_SCANNED
//                ],
//            ])->all();
//
//            if($stocks) {
//                foreach($stocks as $stock) {
//                    $stock->status = Stock::STATUS_INBOUND_NEW;
//                    $stock->primary_address = '';
//                    $stock->save(false);
//                }
//            }
//        }
//    }

    /*
     * Обновляем количество и статус в inbound order item
     * */
//    public function updateInboundOrderItem()
//    {
//        $countStockForItem =  Stock::find()->andWhere([
//            'inbound_order_id' => $this->order_number,
//            'product_barcode' => $this->product_barcode,
//            'inbound_client_box'=>$this->client_box_barcode,
//            'status' => [Stock::STATUS_INBOUND_SCANNED,Stock::STATUS_INBOUND_OVER_SCANNED],
//            'client_id' => $this->client_id,
//        ])->count();
//
//        $inboundOrderItem = InboundOrderItem::find()->andWhere([
//            'inbound_order_id' => $this->order_number,
//            'product_barcode' => $this->product_barcode,
//            'box_barcode'=>$this->client_box_barcode,
//        ])->one();
//
//        if ($inboundOrderItem) {
//
//            if(intval($inboundOrderItem->accepted_qty) < 1) {
//                $inboundOrderItem->begin_datetime = time();
//                $inboundOrderItem->status = Stock::STATUS_INBOUND_NEW;
//            }
//
//            $inboundOrderItem->accepted_qty = $countStockForItem;
//
//            if($inboundOrderItem->accepted_qty == $inboundOrderItem->expected_qty) {
//                $inboundOrderItem->status = Stock::STATUS_INBOUND_SCANNED;
//            }
//
//            $inboundOrderItem->end_datetime = time();
//            $inboundOrderItem->save(false);
//        }
//        $this->setInboundOrderItemModel($inboundOrderItem);
//        return $inboundOrderItem;
//    }

    /*
     * Обновляем количество и статус в inbound order item
     * */
//    public function updateInboundOrderItems()
//    {
//        $dataScannedProductByBarcode = [];
//
//        $productsInBox = Stock::find()
//            ->select('count(product_barcode) as product_barcode_count, product_barcode, inbound_client_box')
//            ->andWhere([
//                'primary_address' => $this->box_barcode,
//                'inbound_order_id' => $this->order_number,
//                'status' => [
//                    Stock::STATUS_INBOUND_SCANNED
//                ]])
//            ->groupBy('product_barcode, inbound_client_box')
//            ->all();
//        if ($productsInBox) {
//
//            foreach ($productsInBox as $stock) {
//                $inboundOrderItem = InboundOrderItem::findOne([
//                        'product_barcode' => $stock->product_barcode,
//                        'inbound_order_id' => $this->order_number,
//                        'box_barcode' =>   $stock->inbound_client_box
//                    ]
//                );
//
//                if ($inboundOrderItem) {
//
//                    $this->removeAllProductsFromBox($stock->product_barcode,$stock->inbound_client_box);
//                    $inboundOrderItem->accepted_qty = $this->getScannedProductInItem($stock->inbound_client_box,$stock->product_barcode);
//                    $inboundOrderItem->save(false);
//
//                    $colorRowClass = 'alert-danger';
//                    if ($inboundOrderItem->accepted_qty == $inboundOrderItem->expected_qty) {
//                        $colorRowClass = 'alert-success';
//                    } elseif ($inboundOrderItem->accepted_qty > $inboundOrderItem->expected_qty) {
//                        $colorRowClass = 'alert-warning';
//                    }
//
//                    $countValue = $inboundOrderItem->accepted_qty;
//                    $rowId = $inboundOrderItem->id . '-' . $stock->product_barcode;
//
//                    $dataScannedProductByBarcode [] = [
//                        'rowId' => $rowId,
//                        'countValue' => $countValue,
//                        'colorRowClass' => $colorRowClass
//                    ];
//                };
//            }
//        }
//
//        return $dataScannedProductByBarcode;
//    }

    /*
     * */
//    public function updateInboundOrder()
//    {
//        if($inboundModel = InboundOrder::findOne($this->order_number)) {
//
//            if(intval($inboundModel->accepted_qty) < 1) {
//                $inboundModel->begin_datetime = time();
//                $inboundModel->status = Stock::STATUS_INBOUND_SCANNING;
//            }
//
//            $inboundModel->accepted_qty = $this->getAllScannedProductInOrder();
//
//            if( $inboundModel->accepted_qty == $inboundModel->expected_qty) {
//                $inboundModel->status = Stock::STATUS_INBOUND_SCANNED;
//            }
//
//            $inboundModel->end_datetime = time();
//            $inboundModel->save(false);
//        }
//        $this->setInboundOrderModel($inboundModel);
//        return $inboundModel;
//    }

    /*
     *
     * */
//    public function updateConsignmentInboundOrders()
//    {
//        $inboundModel = $this->getInboundOrderModel();
//        $coi = ConsignmentInboundOrders::findOne($inboundModel->consignment_inbound_order_id);
//        if($coi) {
//
//            $inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inboundModel->consignment_inbound_order_id])->asArray()->column();
//
//            $coi->accepted_qty = Stock::find()->where([
//                'inbound_order_id' => $inboundIDs,
//                'status' => [
//                    Stock::STATUS_INBOUND_SCANNED,
//                    Stock::STATUS_INBOUND_OVER_SCANNED,
//                ],
//                'client_id' => $this->client_id,
//            ])->count();
//
//            $coi->save(false);
//        }
//        return $coi;
//    }



    /*
    * Get count product in box
    * @param string $boxBarcode
    * @return integer
    *
    * */
//    public function getScannedProductInBox()
//    {
//        return (int)Stock::find()->andWhere([
//            'inbound_order_id'=>$this->order_number,
//            'primary_address' => $this->box_barcode,
//            'status'=>
//                [
//                    Stock::STATUS_INBOUND_SCANNED,
//                    Stock::STATUS_INBOUND_OVER_SCANNED,
//                ]])->count();
//
//    }

    /*
    * Get count product in box
    * @param string $clientBoxBarcode
    * @return integer
    *
    * */
//    public function getScannedProductInItem($clientBoxBarcode,$product_barcode)
//    {
//        return (int)Stock::find()->andWhere([
//            'inbound_order_id'=>$this->order_number,
//            'primary_address' => $this->box_barcode,
//            'inbound_client_box' => $clientBoxBarcode,
//            'product_barcode' => $product_barcode,
//            'status'=>
//                [
//                    Stock::STATUS_INBOUND_SCANNED,
//                    Stock::STATUS_INBOUND_OVER_SCANNED,
//                ]])->count();
//    }

    /*
     * */
//    public function getInboundOrderModel() {
//        return $this->inboundOrderModel;
//    }

    /*
     * */
//    public function setInboundOrderModel($m)
//    {
//        return $this->inboundOrderModel = $m;
//    }

    /*
     * */
//    public function getInboundOrderItemModel()
//    {
//        return $this->inboundOrderItemModel;
//    }

    /*
     * */
//    public function setInboundOrderItemModel($m)
//    {
//        return $this->inboundOrderItemModel = $m;
//    }

    /*
     * */
//    public function getStockModel()
//    {
//        return $this->stockModel;
//    }

    /*
     * */
//    public function setStockModel($m)
//    {
//        return $this->stockModel = $m;
//    }
}