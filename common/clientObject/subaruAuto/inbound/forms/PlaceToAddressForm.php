<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */

namespace common\clientObject\subaruAuto\inbound\forms;

use common\clientObject\subaruAuto\inbound\validation\PlaceToAddressValidation;
use Yii;
use yii\base\Model;

class PlaceToAddressForm extends Model
{
    private $validation;

    public $fromPlaceAddress; // From address
    public $toPlaceAddress; // To address

    //
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->validation = new PlaceToAddressValidation();
    }
    //
    public function rules()
    {
        return [
            // From place address
            [['fromPlaceAddress'], 'required', 'on' => 'onFromAddress'],
            [['fromPlaceAddress'], 'string', 'on' => 'onFromAddress'],
            [['fromPlaceAddress'], 'trim', 'on' => 'onFromAddress'],
            [['fromPlaceAddress'], 'validateFromAddress', 'on' => 'onFromAddress'],
            // To place address
            [['fromPlaceAddress', 'toPlaceAddress'], 'required', 'on' => 'onToPlaceAddress'],
            [['toPlaceAddress'], 'string', 'on' => 'onToPlaceAddress'],
            [['toPlaceAddress'], 'trim', 'on' => 'onToPlaceAddress'],
            [['toPlaceAddress'], 'validateToPlaceAddress', 'on' => 'onToPlaceAddress'],
        ];
    }
    //
    public function validateFromAddress($attribute,$params)
    {
        $fromPlaceAddress = $this->fromPlaceAddress;

        // + Проверяем что это транспартная единица
        if(!$this->validation->isTransportedBoxOrInboundBox($fromPlaceAddress)) {
            $this->addError($attribute, '<b>[' . $fromPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Это не транспортная тара или не приходной шк'));
        }

        // Проверяем находится ли данная транспартная единица в стадии работы
        if($this->validation->isTransportedBoxBarcode($fromPlaceAddress)) {
            if (!$this->validation->isWorkTransportedBoxBarcodeAndNotEmptyUnitFlow($fromPlaceAddress)) {
                $this->addError($attribute, '<b>[' . $fromPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Эта транспортная тара пуста'));
            }
        }
        if($this->validation->isInboundUnitAddress($fromPlaceAddress)) {
            if ($this->validation->isEmptyInboundUnitAddress($fromPlaceAddress)) {
                $this->addError($attribute, '<b>[' . $fromPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст'));
            }
        }
    }
    //
    public function validateToPlaceAddress($attribute, $params)
    {
        $fromPlaceAddress = $this->fromPlaceAddress;
        $toPlaceAddress = $this->toPlaceAddress;

        if($this->validation->isInboundUnitAddress($fromPlaceAddress)) {
            if (!$this->validation->isRackAddressExist($toPlaceAddress)) {
                $this->addError($attribute, '<b>[' . $toPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Это не адрес полки'));
            }
        }
//
        if($this->validation->isTransportedBoxBarcode($fromPlaceAddress)) {
            if(!$this->validation->isInboundUnitAddress($toPlaceAddress)) {
                $this->addError($attribute, '<b>[' . $toPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Это не адрес короба или паллеты'));
            }
        }
    }
    //
    public function attributeLabels()
    {
        return [
            'fromPlaceAddress' => Yii::t('inbound/forms', 'ШК транспортной тары'),
            'toPlaceAddress' => Yii::t('inbound/forms', 'Адрес полки'),
        ];
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->fromPlaceAddress = $this->fromPlaceAddress;
        $dto->toPlaceAddress = $this->toPlaceAddress;
        return $dto;
    }
}