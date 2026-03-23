<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\b2b\domains\outboundLogitrans\forms;

use common\b2b\domains\outboundLogitrans\service\OutboundLogiTransService;
use common\components\BarcodeManager;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\outbound\service\OutboundBoxService;
use yii\base\Model;
use Yii;
use common\modules\codebook\models\Codebook;
use common\modules\stock\models\Stock;
use yii\helpers\VarDumper;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\inbound\models\InboundOrder;
use common\modules\client\models\Client;


class ScanningForm extends Model {

    public $employee_barcode = '01';
    public $picking_list_barcode;
    public $picking_list_barcode_scanned;
    public $box_barcode;
    public $product_barcode;
    public $step;
    public $box_kg;

    /*
     *
     * */
    public function rules()
    {
        return [
            [['picking_list_barcode_scanned','employee_barcode','picking_list_barcode','box_barcode','product_barcode','step','box_kg'], 'trim'],
            [['picking_list_barcode_scanned','employee_barcode','picking_list_barcode','box_barcode','product_barcode','box_kg'], 'string'],
            [['step'], 'integer'],
            [['box_kg'], 'number'],
            [['employee_barcode'],'IsEmployeeBarcode', 'on'=>'IsEmployeeBarcode'],
            [['employee_barcode'],'required', 'on'=>'IsEmployeeBarcode'],
            [['picking_list_barcode'],'IsPickingListBarcode', 'on'=>'IsPickingListBarcode'],
            [['picking_list_barcode'],'required', 'on'=>'IsPickingListBarcode'],
            [['box_barcode'],'IsBoxBarcode', 'on'=>'IsBoxBarcode'],
            [['box_barcode'],'required', 'on'=>'IsBoxBarcode'],

            [['product_barcode'],'IsProductBarcode', 'on'=>'IsProductBarcode'],
            [['box_barcode'],'IsBoxBarcode', 'on'=>'IsProductBarcode'],
            [['product_barcode'],'required', 'on'=>'IsProductBarcode'],
            [['box_barcode'],'required', 'on'=>'IsProductBarcode'],

            [['box_barcode'], 'required','on'=>'ClearBox'],
            [['box_barcode'], 'IsBoxBarcode','on'=>'ClearBox'],
            [['box_barcode'], 'validateClearBox','on'=>'ClearBox'],
            [['box_barcode','product_barcode'], 'required','on'=>'ClearProductInBox'],
            [['product_barcode'], 'validateProductInBox','on'=>'ClearProductInBox'],
            [['box_barcode','box_kg'], 'required','on'=>'sSaveBoxKg'],
            [['employee_barcode'], 'required','on'=>'sPrintBoxKgList'],


        ];
    }

    function getPickingList() {
         $service =  new OutboundLogiTransService();
         return $service->getPickingListReadyForScanned();
    }

    /*
    * Remove product in box
    *
    * */
    public function validateProductInBox($attribute, $params)
    {
        $value = $this->$attribute;
        $box_barcode = $this->box_barcode;
        if(!self::checkProductInBox($value,$box_barcode)) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('outbound/errors','Этого товара нет в выбранном коробе ['.$box_barcode.'] или для этого короба распечатали этикетки')); // Этого товара нет в укзанном коробе
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
    public function IsPickingListBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isPickingList($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод сборочного листа'));
        }

        if(BarcodeManager::isPickingList($value,[OutboundPickingLists::STATUS_NOT_SET,OutboundPickingLists::STATUS_BEGIN,OutboundPickingLists::STATUS_PRINT])) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели еще не собраный штрихкод сборочного листа'));
        }
        if(BarcodeManager::isPickingList($value,OutboundPickingLists::STATUS_PRINT_BOX_LABEL)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Этот сборочный лист уже упакован'));
        }

//        die("--=-=-=-=--=-=-=-=-=-=-=-=-=-=-=-=");
        if ($opl = OutboundPickingLists::findOne(['barcode' => $this->picking_list_barcode, 'status' => OutboundPickingLists::STATUS_END])) {

            $plIds = (empty($this->picking_list_barcode_scanned) ? '' : $this->picking_list_barcode_scanned . ',') . $opl->id;

            if($plIds) {
                $qIDs = OutboundPickingLists::prepareIDsHelper($plIds);
                if( !empty($qIDs) && is_array($qIDs)) {
                    $opl = OutboundPickingLists::find()->select('outbound_order_id')->where(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->count();
                    if($opl >= 2) {
                        $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели штрихкод сборочного листа из другого заказа'));
                    }
                    //S: TODO Сделать это по нормально
                    if($ooIDs = OutboundPickingLists::find()->select('outbound_order_id')->where(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->column()) {
                        if($oos = OutboundOrder::findAll($ooIDs)) {
                            foreach($oos as $o) {
                                if(!in_array($o->status,[
                                    Stock::STATUS_OUTBOUND_PICKING,
                                    Stock::STATUS_OUTBOUND_PICKED,
                                    Stock::STATUS_OUTBOUND_SCANNING,
                                    Stock::STATUS_OUTBOUND_SCANNED,
                                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                                ])) {
                                    $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели штрихкод сборочного листа который уже отсканирован'));
                                }
                            }
                        }
                    }
                    //E: TODO Сделать это по нормально
                }
            }
        }
    }

    /*
    * Validate barcode picking list
    * */
    public function IsBoxBarcode($attribute, $params)
    {
        $value = $this->$attribute;
//        $value = $this->box_barcode;
        if(!BarcodeManager::isBox($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод короба'));
        }

        if($box = OutboundBoxService::getByBarcode($value)) {
            if(!empty($box->client_extra_json)) {
                $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Этот шк уже отгружен. Используйте новый шк короба'));
            }
        } else {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод короба для отгрузки ДЕфакто'));
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
        if(!BarcodeManager::isProduct($value) && !BarcodeManager::isDefactoBox($value) && !BarcodeManager::isM3BoxBorder($value) && !BarcodeManager::isBoxOnlyOur($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод товара'));
        }
        if(!BarcodeManager::isProduct($value) && !BarcodeManager::isDefactoBox($value) && !BarcodeManager::isBoxOnlyOur($value) && !BarcodeManager::isM3BoxBorder($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод товара'));
        }


        if(BarcodeManager::isDefactoBox($value)) {
            if(!$this->isReturnBox($value)) {
                $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод короба клиента'));
            };
        }

        if(BarcodeManager::isBoxOnlyOur($value)) {
            $clientBox = $this->getClientBoxByOutBox($value);
            if(!$this->isReturnBox($clientBox) && !$this->isOneBoxLot($value)) {
                $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод короба клиента или нашего. Или это лишний лот'));
            };
        }
    }


    public function isReturnBox($boxBarcode) {
        if(InboundOrder::find()->andWhere(['order_number'=>$boxBarcode,'order_type'=>InboundOrder::ORDER_TYPE_RETURN,'client_id'=>Client::CLIENT_DEFACTO])->exists()) {
            if(ReturnOrderItems::find()->andWhere(['client_box_barcode'=>$boxBarcode])->exists()) {
                return Stock::find()->andWhere([
                    'client_id'=>Client::CLIENT_DEFACTO,
                    'status' => [
                        Stock::STATUS_OUTBOUND_PICKED,
                        Stock::STATUS_OUTBOUND_SCANNING
                    ],
                    'inbound_client_box' => $boxBarcode,
                    'outbound_picking_list_id' => OutboundPickingLists::prepareIDsHelper($this->picking_list_barcode_scanned)
                ])->exists();
            }
        }

        return false;
    }

    public function getClientBoxByOutBox($outBoxBarcode) {
        return Stock::find()->select('inbound_client_box')->andWhere([
            'client_id'=>Client::CLIENT_DEFACTO,
            'status' => [
                Stock::STATUS_OUTBOUND_PICKED,
                Stock::STATUS_OUTBOUND_SCANNING
            ],
            'primary_address' => $outBoxBarcode,
            'outbound_picking_list_id' => OutboundPickingLists::prepareIDsHelper($this->picking_list_barcode_scanned)
        ])->scalar();
    }

    public function isOneBoxLot($outBoxBarcode) {

       return   Stock::find()->andWhere([
            'client_id'=>Client::CLIENT_DEFACTO,
//            'status' => [
//                Stock::STATUS_OUTBOUND_PICKED,
//                Stock::STATUS_OUTBOUND_SCANNING
//            ],
            'primary_address' => $outBoxBarcode,
//            'outbound_picking_list_id' => OutboundPickingLists::prepareIDsHelper($this->picking_list_barcode_scanned)
        ])->count() == 1;
    }

    /*
    *
    *
    * */
    public function attributeLabels()
    {
        return [
            'employee_barcode' => Yii::t('outbound/forms', 'Employee barcode'),
            'picking_list_barcode' => Yii::t('outbound/forms', 'Picking list barcode'),
            'box_barcode' => Yii::t('outbound/forms', 'Box barcode'),
            'product_barcode' => Yii::t('outbound/forms', 'Product barcode'),
            'box_kg' => Yii::t('outbound/forms', 'BOX_KG'),
        ];
    }

    /*
    * Validate barcode employee
    * */
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


    /*
    * Check exist product in box
    * @param string $productBarcode
    * @return boolean
    * */
    public function checkProductInBox($productBarcode,$box_barcode)
    {
        return Stock::find()->where(['box_barcode'=>$box_barcode,'product_barcode'=>$productBarcode,'status'=>Stock::STATUS_OUTBOUND_SCANNED])->exists();
    }
}