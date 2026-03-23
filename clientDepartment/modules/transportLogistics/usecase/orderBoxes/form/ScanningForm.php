<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace app\modules\transportLogistics\usecase\orderBoxes\form;

use clientDepartment\modules\transportLogistics\usecase\orderBoxes\Service as OrderBoxService;
use common\components\BarcodeManager;
use yii\base\Model;
use Yii;


class ScanningForm extends Model {
    public $employee_name;
    public $box_barcode;
    public $delivery_proposal_id;
    /*
     *
     * */
    public function rules()
    {
        return [
            [['employee_name','box_barcode','delivery_proposal_id'], 'trim'],
            [['employee_name','box_barcode','delivery_proposal_id'], 'string'],
            [['employee_name'],'required', 'on'=>'IsEmployeeName'],
            [['box_barcode'],'IsBoxBarcode', 'on'=>'IsBoxBarcode'],
            [['box_barcode'],'required', 'on'=>'IsBoxBarcode'],
        ];
    }

    /*
    * Validate barcode picking list
    * */
    public function IsBoxBarcode($attribute, $params)
    {
        $value = $this->box_barcode;
        if(!BarcodeManager::isBox($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод короба'));
        }

        if((new OrderBoxService())->isBoxExists($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы уже сканировали этот короб'));
        }
    }

    /*
    * */
    public function attributeLabels()
    {
        return [
            'employee_name' => Yii::t('outbound/forms', 'Имя сотрудника'),
            'box_barcode' => Yii::t('outbound/forms', 'Box barcode'),
        ];
    }
}