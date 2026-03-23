<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\clientObject\hyundaiAuto\outbound\forms;

use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundPickingLists;
use yii\base\Model;
use Yii;

class BeginEndPickListForm extends Model {

    public $picking_list_barcode;
    public $employee_barcode;

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

        if($oo = OutboundPickingLists::find()->andWhere(['barcode'=>$barcode])->one()) {
            $barcodePL = $oo->barcode;
            $status = $oo->status;
        } else if($oo = OutboundPickingLists::find()->andWhere(['barcode'=>$barcode_finish])->one()) {
            $barcodePL = $oo->barcode;
            $status = $oo->status;
        }

        if($e = Employees::find()->andWhere(['barcode'=>$barcode])->one()) {
            $barcodeE = $e->barcode;
        } else if($e = Employees::find()->andWhere(['barcode'=>$barcode_finish])->one()) {
            $barcodeE = $e->barcode;
        }

        if( empty($barcodePL) && empty($barcodeE) ) {
            $this->addError($attribute, '<b> ['.$barcode.'] </b> ' . Yii::t('outbound/errors','Вы ввели не существующий штрих код. Вы должны ввести штрих-код свой или уборочного листа ') );
        }
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
     * */
    public function rules()
    {
        return [
            [['barcode_process'], 'required'],
            [['picking_list_barcode','employee_barcode'], 'string'],
            [['picking_list_barcode','employee_barcode'], 'trim'],
        ];
    }
}