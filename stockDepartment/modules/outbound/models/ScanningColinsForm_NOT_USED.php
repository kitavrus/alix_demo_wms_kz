<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\outbound\models;

use common\components\BarcodeManager;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\product\models\ProductBarcodes;
use yii\base\Model;
use Yii;
use common\modules\codebook\models\Codebook;
use common\modules\stock\models\Stock;
use yii\helpers\VarDumper;
use common\modules\client\models\Client;


class ScanningColinsForm extends Model {

    public $employee_barcode;
    public $order_shop;
    public $box_barcode;
    public $product_barcode;

    /*
     *
     * */
    public function rules()
    {
        return [
            [['employee_barcode','order_shop','box_barcode','product_barcode'], 'trim'],
            [['employee_barcode','order_shop','box_barcode','product_barcode'], 'string'],
            [['employee_barcode'],'IsEmployeeBarcode', 'on'=>'IsEmployeeBarcode'],
            [['employee_barcode'],'required', 'on'=>'IsEmployeeBarcode'],

            [['order_shop'],'required', 'on'=>'IsBoxBarcode'],
            //[['order_shop'],'required', 'on'=>'IsProductBarcode'], // Outbound order id

            [['order_shop'],'required', 'on'=>'IsOrderShop'], // Outbound order id


            [['box_barcode'],'IsBoxBarcode', 'on'=>'IsBoxBarcode'],
            [['box_barcode'],'required', 'on'=>'IsBoxBarcode'],
            [['box_barcode'],'required', 'on'=>'IsProductBarcode'],
            [['box_barcode'], 'required','on'=>'ClearBox'],
            [['box_barcode'], 'IsBoxBarcode','on'=>'ClearBox'],
            [['box_barcode'], 'validateClearBox','on'=>'ClearBox'],
            [['box_barcode','product_barcode'], 'required','on'=>'ClearProductInBox'],

            [['product_barcode'], 'validateProductInOrder','on'=>'ClearProductInBox'],
            [['product_barcode','order_shop','employee_barcode'],'required', 'on'=>'IsProductBarcode'],
            [['product_barcode'],'IsProductBarcode', 'on'=>'IsProductBarcode'],
            [['product_barcode'],'validateProductInOrder', 'on'=>'IsProductBarcode'],

        ];
    }

    /*
    * Remove product in box
    *
    * */
    public function validateProductInOrder($attribute, $params)
    {
        $value = $this->$attribute;
        $outboundOrderId = $this->order_shop;
        if( !($order = self::checkProductInOrder($value,$outboundOrderId)) ) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('outbound/errors','Этого товара ['.$value.'] нет в заказе'));
       // } else if($order->allocated_qty == $order->expected_qty) {
//        } else if($order->accepted_qty == $order->expected_qty || $order->accepted_qty == $order->allocated_qty) {
        } else if($order->accepted_qty == $order->expected_qty) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('outbound/errors','Этот товар ['.$value.'] лишний в заказе'));
        }
    }

    /*
    * Validate barcode employee
    * */
    public function IsEmployeeBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isEmployee($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод сотрудника'));
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
    * Validate barcode product
    * */
    public function IsProductBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isProduct($value) && !BarcodeManager::isM3BoxBorder($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод товара'));
        }
    }

    /*
    *
    *
    * */
    public function attributeLabels()
    {
        return [
            'employee_barcode' => Yii::t('outbound/forms', 'Employee barcode'),
            'order_shop' => Yii::t('outbound/forms', 'Order shop'),
            'box_barcode' => Yii::t('outbound/forms', 'Box barcode'),
            'product_barcode' => Yii::t('outbound/forms', 'Product barcode'),
        ];
    }

    /*
    * Validate barcode employee
    * */
    /*
    public function validateIsEmpty($attribute, $params)
    {
        $barcode = $this->barcode;
        $barcode_finish = $this->barcode_finish;
        if( empty($barcode) && empty($barcode_finish) ) {
            $this->addError($attribute, '<b> ['.$barcode.'] </b> ' . Yii::t('outbound/errors','Пожалуйста укажите штрих-код сборочного листа или сотрудника') );
        }


        if($oo = OutboundPickingLists::find()->where(['barcode'=>$barcode])->one()) {
            $barcodePL = $oo->barcode;
            $status = $oo->status;
        } else if($oo = OutboundPickingLists::find()->where(['barcode'=>$barcode_finish])->one()) {
            $barcodePL = $oo->barcode;
            $status = $oo->status;
        }

        if($e = Employees::find()->where(['barcode'=>$barcode])->one()) {
            $barcodeE = $e->barcode;
        } else if($e = Employees::find()->where(['barcode'=>$barcode_finish])->one()) {
            $barcodeE = $e->barcode;
        }

        if( empty($barcodePL) && empty($barcodeE) ) {
            $this->addError($attribute, '<b> ['.$barcode.'] </b> ' . Yii::t('outbound/errors','Вы ввели не существующий штрих код. Вы должны ввести штрих-код свой или уборочного листа ') );
        }
    }
    */

    /*
    * Check exist product in box
    * @param string $productBarcode
    * @param integer $outboundOrderId
    * @return boolean
    * */
    public function checkProductInOrder($productBarcode,$outboundOrderId)
    {
       return OutboundOrderItem::find()->where(['product_barcode'=>$productBarcode,'outbound_order_id'=>$outboundOrderId])->one();
//       return OutboundOrderItem::find()->where(['product_barcode'=>$productBarcode,'outbound_order_id'=>$outboundOrderId])->andWhere('allocated_qty != expected_qty')->exists();

//        return Stock::find()->where(['box_barcode'=>$box_barcode,'product_barcode'=>$productBarcode,'status'=>Stock::STATUS_OUTBOUND_SCANNED])->exists();
    }


    /*
    * Ищем на стоке частично зарезерв. запись для
    * сканирования
    * */
    public function findStockForScanning()
    {
        return Stock::find()->where([
            'status' => [
                Stock::STATUS_INBOUND_SORTED,
            ],
            'status_availability' => Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED,
            'product_barcode' => $this->product_barcode,
            'outbound_order_id' => $this->order_shop
        ])
            ->one();
    }

    /*
     * Ищем в заказе зарезервированный item
     **/
    public function findReservedItem()
    {
        return OutboundOrderItem::find()
                ->andWhere([
                        'outbound_order_id' => $this->order_shop,
                        'product_barcode' => $this->product_barcode
                    ])
                ->andWhere('allocated_qty > 0')
                ->exists();
    }

    /*
    * Создаем запись на стоке для сканирования
    **/
    public function createStockItemForScanning()
    {
        $stock = new Stock();
        $stock->outbound_order_id = $this->order_shop;
        $stock->client_id = Client::CLIENT_COLINS;
        $stock->product_barcode = $this->product_barcode;
        $stock->status_availability =  Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED;
        $stock->status = Stock::STATUS_INBOUND_SORTED;
        if($product = ProductBarcodes::getProductByBarcode(Client::CLIENT_COLINS, $this->product_barcode)){
            $stock->product_model = $product->model;
        }
        $stock->save(false);

        return $stock;
    }
}