<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.08.2020
 * Time: 8:10
 */

namespace common\b2b\domains\outboundLogitrans\forms;

use common\b2b\domains\outbound\validation\CargoDeliveryValidator;
use yii\base\Model;
use Yii;

class OutboundLogiTransForm extends Model
{
    public $keyCargoDelivery;
    public $selectOrder;
    public $boxBarcode;

    const SCENARIO_SELECT_ORDER = 'SELECT-ORDER';
    const SCENARIO_BOX_BARCODE = 'BOX-BARCODE';
    const SCENARIO_PRINT = 'PRINT';
    const SCENARIO_SHOW_BOX_IN_ORDER = 'SHOW-BOX-IN-ORDER';
    const SCENARIO_SHOW_ALL_SCANNED_BOX = 'SHOW-ALL-SCANNED-BOX';

    private $validation;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->validation = new CargoDeliveryValidator();
        $this->keyCargoDelivery = date('d-m-Y');
    }

    /*
     * */
    public function rules()
    {
        return [
            [['keyCargoDelivery','selectOrder'], 'trim', 'on' => self::SCENARIO_SELECT_ORDER],
            [['keyCargoDelivery','selectOrder'], 'string', 'on' => self::SCENARIO_SELECT_ORDER],
            [['keyCargoDelivery','selectOrder'], 'required', 'on' => self::SCENARIO_SELECT_ORDER],
            ['selectOrder', 'SelectOrder_', 'on' => self::SCENARIO_SELECT_ORDER],

            [['keyCargoDelivery', 'selectOrder', 'boxBarcode'], 'trim', 'on' => self::SCENARIO_BOX_BARCODE],
            [['keyCargoDelivery', 'selectOrder', 'boxBarcode'], 'string', 'on' => self::SCENARIO_BOX_BARCODE],
            [['keyCargoDelivery', 'selectOrder','boxBarcode'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],
            ['boxBarcode', 'BoxBarcode_', 'on' => self::SCENARIO_BOX_BARCODE],
            ['selectOrder', 'SelectOrder_', 'on' => self::SCENARIO_BOX_BARCODE],

            [['keyCargoDelivery'], 'required', 'on' => self::SCENARIO_PRINT],

            [['selectOrder'], 'required', 'on' => self::SCENARIO_SHOW_BOX_IN_ORDER],
            [['keyCargoDelivery'], 'required', 'on' => self::SCENARIO_SHOW_ALL_SCANNED_BOX],

        ];
    }

    public function SelectOrder_($attribute, $params) {
        $keyCargoDelivery = $this->keyCargoDelivery;
        $selectOrder = $this->selectOrder;

        $is = $this->validation->isNotValidOrder($keyCargoDelivery,$selectOrder);
        if ($is->isNotValid) {
            $this->addError($attribute, '<b>[' . $selectOrder . ']</b> ' . Yii::t('outbound/errors', $is->errorMessage));
        }
    }

    public function BoxBarcode_($attribute, $params) {

        $keyCargoDelivery = $this->keyCargoDelivery;
        $selectOrder = $this->selectOrder;
        $boxBarcode = $this->boxBarcode;

        $is = $this->validation->isNotValidScannedBoxBarcode($selectOrder,$boxBarcode);
        if ($is->isNotValid) {
            $this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', $is->errorMessage));
        }
    }

    /*
    *
    * */
    public function attributeLabels()
    {
        return [
            'keyCargoDelivery' => Yii::t('outbound/forms', 'Дата отгрузки'),
            'selectOrder' => Yii::t('outbound/forms', 'Заказ на отгрузку'),
            'boxBarcode' => Yii::t('outbound/forms', 'Шк короба 8000...'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->keyCargoDelivery = $this->keyCargoDelivery;
        $dto->selectOrder = $this->selectOrder;
        $dto->boxBarcode = $this->boxBarcode;
        return $dto;
    }
}