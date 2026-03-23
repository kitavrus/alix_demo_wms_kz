<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */
namespace common\ecommerce\defacto\changeAddressPlace\forms;

use common\ecommerce\defacto\changeAddressPlace\validation\Validation;
use Yii;
use yii\base\Model;

class BoxToPlaceForm extends Model
{
    private $validation;

    public $fromPlaceAddress; // From address
    public $toPlaceAddress; // To address

    //
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->validation = new Validation();
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

        if(!$this->validation->ourBoxBarcode($fromPlaceAddress)) {
            $this->addError($attribute, '<b>[' . $fromPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Это не наш короб'));
            return;
        }

        if(!$this->validation->isBoxNotEmpty($fromPlaceAddress)) {
            $this->addError($attribute, '<b>[' . $fromPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст'));
        }
    }
    //
    public function validateToPlaceAddress($attribute, $params)
    {
//        $fromPlaceAddress = $this->fromPlaceAddress;
        $toPlaceAddress = $this->toPlaceAddress;

        if(!$this->validation->palaceAddress($toPlaceAddress)) {
            $this->addError($attribute, '<b>[' . $toPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Это не адрес  4-го этажа'));
            return;
        }

        if(!$this->validation->isExistPalaceAddress($toPlaceAddress)) {
            $this->addError($attribute, '<b>[' . $toPlaceAddress . ']</b> ' . Yii::t('inbound/errors', 'Это не адрес полки'));
        }
    }
    //
    public function attributeLabels()
    {
        return [
            'fromPlaceAddress' => Yii::t('inbound/forms', 'Шк короба'),
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