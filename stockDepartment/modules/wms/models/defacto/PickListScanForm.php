<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 30.08.2017
 * Time: 8:45
 */

namespace stockDepartment\modules\wms\models\defacto;

use stockDepartment\modules\wms\models\defacto\PickList\repository\PickListRepository;
use Yii;

class PickListScanForm extends \yii\base\Model
{
    public $pickListBarcode;
    public $lotBarcode;

    /*
     * */
    public function rules()
    {
        return [
            [['pickListBarcode'], 'required','on'=>'sPickListBarcode'],
            [['pickListBarcode'], 'string','on'=>'sPickListBarcode'],
            [['pickListBarcode'], 'trim','on'=>'sPickListBarcode'],
            [['pickListBarcode'], 'validatePickListBarcode','on'=>'sPickListBarcode'],

            [['lotBarcode','pickListBarcode'], 'required','on'=>'sLotBarcode'],
            [['lotBarcode','pickListBarcode'], 'string','on'=>'sLotBarcode'],
            [['lotBarcode','pickListBarcode'], 'trim','on'=>'sLotBarcode'],
            [['lotBarcode'], 'validateLotBarcode','on'=>'sLotBarcode'],
        ];
    }

    public function validatePickListBarcode($attribute, $params) {
        $pickListBarcode = $this->$attribute;

        if(!PickListRepository::existPickList($pickListBarcode)) {
            $this->addError($attribute, '<b>['.$pickListBarcode.']</b> '.Yii::t('inbound/errors','"Лист сборки не найден"'));
            return true;
        }

        if(!PickListRepository::isNew($pickListBarcode) && !PickListRepository::isInProcess($pickListBarcode)) {
            $this->addError($attribute, '<b>['.$pickListBarcode.']</b> '.Yii::t('inbound/errors','"Этот лист сборки уже собран"'));
        }
        return true;
    }

    public function validateLotBarcode($attribute, $params) {
        $lotBarcode = $this->lotBarcode;
        $pickListBarcode = $this->pickListBarcode;
        if(!PickListRepository::existLotBarcode($pickListBarcode,$lotBarcode)) {
            $this->addError($attribute, '<b>['.$lotBarcode.']</b> '.Yii::t('inbound/errors','"Не верный лот или лишний лот"'));
        }
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->pickListBarcode = $this->pickListBarcode;
        $dto->lotBarcode = $this->lotBarcode;
        return $dto;
    }


    /*
     * */
    public function attributeLabels()
    {
        return [
            'pickListBarcode' => Yii::t('inbound/forms', 'Лист сборки штрих-код'),
            'lotBarcode' => Yii::t('inbound/forms', 'Лот штрих-код'),
        ];
    }
}