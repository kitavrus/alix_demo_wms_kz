<?php
namespace app\modules\intermode\controllers\outbound\domain;

use common\components\BarcodeManager;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use yii\base\Model;
use Yii;
use common\modules\codebook\models\Codebook;
use common\modules\stock\models\Stock;
use yii\helpers\VarDumper;


class ScanningForm extends Model {

    public $employee_barcode;
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
            [['box_barcode'],'validateBoxBarcodeOnly4000', 'on'=>'IsBoxBarcode'],
            [['product_barcode'],'IsProductBarcode', 'on'=>'IsProductBarcode'],
            [['box_barcode'],'IsBoxBarcode', 'on'=>'IsProductBarcode'],
            [['product_barcode'],'required', 'on'=>'IsProductBarcode'],
            [['box_barcode'],'required', 'on'=>'IsProductBarcode'],
            [['box_barcode'],'validateBoxBarcodeOnly4000', 'on'=>'IsProductBarcode'],
            [['box_barcode'], 'required','on'=>'ClearBox'],
            [['box_barcode'], 'IsBoxBarcode','on'=>'ClearBox'],
            [['box_barcode'], 'validateClearBox','on'=>'ClearBox'],
            [['box_barcode'], 'validateBoxBarcodeOnly4000','on'=>'ClearBox'],
            [['box_barcode','product_barcode'], 'required','on'=>'ClearProductInBox'],
            [['product_barcode'], 'validateProductInBox','on'=>'ClearProductInBox'],
			[['box_barcode','box_kg'], 'required','on'=>'sSaveBoxKg'],
			// Print box label
			[['picking_list_barcode'],'IsPickingListBarcode', 'on'=>'onPrintBoxLabel'],
			[['picking_list_barcode'],'required', 'on'=>'onPrintBoxLabel'],
        ];
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
//                                    Stock::STATUS_OUTBOUND_SCANNED,
//                                    Stock::STATUS_OUTBOUND_FULL_RESERVED,
//                                    Stock::STATUS_OUTBOUND_RESERVING,
//                                    Stock::STATUS_OUTBOUND_PART_RESERVED,
//                                    Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
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
            'picking_list_barcode' => Yii::t('outbound/forms', 'Picking list barcode'),
            'box_barcode' => Yii::t('outbound/forms', 'Box barcode'),
            'product_barcode' => Yii::t('outbound/forms', 'Product barcode')." Напиши готово для печати этикеток",
			'box_kg' => Yii::t('outbound/forms', 'BOX_KG'),
        ];
    }

    /*
    * Validate barcode employee
    * */
//    public function validateIsEmployee($attribute, $params)
//    {
//        $value = $this->$attribute;
//        if(!BarcodeManager::isEmployee($value)) {
//            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод сотрудника'));
//        }
//    }
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
//        if( !($oo = OutboundPickingLists::find()->where('barcode = :barcode OR barcode = :barcode_finish',[':barcode'=>$barcode, ':barcode_finish'=>$barcode_finish])->one())
//                &&
//            !($e = Employees::find()->where('barcode = :barcode OR barcode = :barcode_finish',[':barcode'=>$barcode, ':barcode_finish'=>$barcode_finish])->exists() )
//            ) {
//        }
    }

    /*
    * Проверяет существует ли отсканированный товар в выбранном заказе
    * @param string $productBarcode
    * @return
    * */
//    public function checkProductBarcode($productBarcode)
//    {
//        return OutboundOrderItem::find()->where(['product_barcode'=>$productBarcode])->exists();
//    }

    /*
    * Check exist product in box
    * @param string $productBarcode
    * @return boolean
    * */
    public function checkProductInBox($productBarcode,$box_barcode)
    {
        return Stock::find()->where(['box_barcode'=>$box_barcode,'product_barcode'=>$productBarcode,'status'=>Stock::STATUS_OUTBOUND_SCANNED])->exists();
    }

	/**
	 * Validate box_barcode
	 * */
	public function validateBoxBarcodeOnly4000($attribute, $params){
		$boxBarcode = $this->box_barcode;
		$inboundError = BarcodeManager::isValidOutboundBoxBarcode($boxBarcode);
		if ($inboundError) {
			$this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', $inboundError));
		}
	}
}