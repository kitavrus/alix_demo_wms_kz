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
use common\modules\outbound\models\OutboundPickingLists;
use yii\base\Model;
use Yii;
use common\modules\codebook\models\Codebook;
use common\modules\stock\models\Stock;


class BeginEndPickListForm extends Model {

//    public $outbound_order_id;
//    public $picking_list_id;
//    public $barcode_process;
    public $picking_list_barcode;
//    public $picking_list_id;
    public $employee_barcode;
//    public $employee_id;
//    public $picking_list_barcode;
//    public $employee_barcode;
//    public $employee_id;
//    public $begin_datetime;
//    public $end_datetime;


    /*
    * Validate barcode picking list
    * */
//    public function validateIsPickingList($attribute, $params)
//    {
//        $value = $this->$attribute;
//        if(!BarcodeManager::isPickingList($value)) {
//            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод сборочного листа'));
//        }
//    }

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
     *
     * */
    public function attributeLabels()
    {
        return [
            'barcode' => Yii::t('outbound/forms', 'Barcode'),
        ];
    }

    /*
     *
     *
     * */
    public function rules()
    {
        // Для старна
        // 1 - Вводим ШК сборочного листа
        // 2 - Вводим ШК работника склада
        // При завершении
        // 1 - Вводим ШК работника

        return [
            [['barcode_process'], 'required'],
//            [['barcode_process','picking_list_barcode','employee_barcode'], 'string'],
            [['picking_list_barcode','employee_barcode'], 'string'],
//            [['barcode_process','picking_list_barcode','picking_list_id','employee_barcode','employee_id'], 'trim'],
            [['picking_list_barcode','employee_barcode'], 'trim'],
//            [['barcode','barcode_finish'], 'validateIsEmpty','on'=>'validateIsEmpty'],
//            [['barcode'], 'validateIsEmployee'],
        ];
    }

//    public $picking_list_barcode;
//    public $picking_list_id;
//    public $employee_barcode;
//    public $employee_id;
}