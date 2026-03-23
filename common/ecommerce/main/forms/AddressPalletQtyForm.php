<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */

namespace common\ecommerce\main\forms;

use common\ecommerce\main\validation\AddressPalletQtyValidation;
use Yii;
use yii\base\Model;

class AddressPalletQtyForm extends Model
{
    private $validation;

    public $placeAddress; // place address
    public $palletPlaceQty; // pallet address

    //
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->validation = new AddressPalletQtyValidation();
    }
    //
    public function rules()
    {
        return [
            // From place address
            [['placeAddress'], 'required', 'on' => 'onPlaceAddress'],
            [['placeAddress'], 'string', 'on' => 'onPlaceAddress'],
            [['placeAddress'], 'trim', 'on' => 'onPlaceAddress'],
            [['placeAddress'], 'validatePlaceAddress', 'on' => 'onPlaceAddress'],
            // To place address
            [['placeAddress', 'palletPlaceQty'], 'required', 'on' => 'onPalletPlaceQty'],
            [['palletPlaceQty'], 'integer','min'=>1,'max'=>3, 'on' => 'onPalletPlaceQty'],
            [['palletPlaceQty'], 'trim', 'on' => 'onPalletPlaceQty'],
        ];
    }
    //
    public function validatePlaceAddress($attribute,$params)
    {
        $placeAddress = $this->placeAddress;
        if ($this->validation->isEmptyAddress($placeAddress)) {
            $this->addError($attribute, '<b>[' . $placeAddress . ']</b> ' . Yii::t('inbound/errors', 'Этот адрес пуст'));
        }
    }

    public function attributeLabels()
    {
        return [
            'placeAddress' => Yii::t('inbound/forms', 'Адрес полки'),
            'palletPlaceQty' => Yii::t('inbound/forms', 'Кол-во паллета мест'),
        ];
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->placeAddress = $this->placeAddress;
        $dto->palletPlaceQty = $this->palletPlaceQty;
        return $dto;
    }
}