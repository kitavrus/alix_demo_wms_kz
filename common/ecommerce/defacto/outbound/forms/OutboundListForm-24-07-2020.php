<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\outbound\forms;

//use common\ecommerce\defacto\employee\repository\EmployeeRepository;
//use  common\ecommerce\defacto\outbound\validation\ValidationOutbound;
//use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\defacto\outbound\service\OutboundListService;
use yii\base\Model;
use Yii;

class OutboundListForm extends Model
{
    public $title;
    public $barcode;
    public $courierCompany;

    const SCENARIO_ADD = 'ADD';
    const SCENARIO_COURIER_COMPANY = 'COURIER-COMPANY';
    const SCENARIO_PRINT = 'PRINT';
    const SCENARIO_SHOW_ORDER_IN_LIST = 'SHOW-ORDER-IN-LIST';
    const SCENARIO_SHOW_KASPI_ORDERS = 'SHOW-KASPI-ORDERS';
    const SCENARIO_SHOW_PACKED_ORDER_BUT_NOT_SCANNED_TO_LIST = 'SHOW-PACKED-ORDER-BUT-NOT-SCANNED-TO-LIST';

    private $validation;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->validation = new OutboundListService();
    }

    /*
     * */
    public function rules()
    {
        return [
            [['title', 'courierCompany'], 'trim', 'on' => self::SCENARIO_COURIER_COMPANY],
            [['title', 'courierCompany'], 'string', 'on' => self::SCENARIO_COURIER_COMPANY],
            [['title', 'courierCompany'], 'required', 'on' => self::SCENARIO_COURIER_COMPANY],
            ['courierCompany', 'CourierCompany', 'on' => self::SCENARIO_COURIER_COMPANY],

            [['title', 'barcode'], 'trim', 'on' => self::SCENARIO_ADD],
            [['title', 'barcode'], 'string', 'on' => self::SCENARIO_ADD],
            [['title', 'barcode','courierCompany'], 'required', 'on' => self::SCENARIO_ADD],
            ['barcode', 'Barcode', 'on' => self::SCENARIO_ADD],

            [['title'], 'required', 'on' => self::SCENARIO_PRINT],
            [['courierCompany'], 'required', 'on' => self::SCENARIO_PRINT],

            [['title'], 'required', 'on' => self::SCENARIO_SHOW_ORDER_IN_LIST],
            [['courierCompany'], 'required', 'on' => self::SCENARIO_SHOW_ORDER_IN_LIST],

            [['title'], 'required', 'on' => self::SCENARIO_SHOW_KASPI_ORDERS],
            [['title'], 'required', 'on' => self::SCENARIO_SHOW_PACKED_ORDER_BUT_NOT_SCANNED_TO_LIST],
        ];
    }

    public function CourierCompany($attribute, $params) {
        $title = $this->title;
        $courierCompany = $this->courierCompany;
    }

    /*
    * Validate barcode employee
    * */
    public function Barcode($attribute, $params)
    {
        $title = $this->title;
        $barcode = $this->barcode;
        $courierCompany = $this->courierCompany;

        if ($this->validation->isListNotPrinted($title,$courierCompany)) {
            $this->addError($attribute, '<b>[' . $barcode . ']</b> ' . Yii::t('outbound/errors', 'Этот лист отгрузки уже распечатан'));
            return;
        }

        if ($this->validation->isPackageBarcodeExistInOtherList($title,$barcode,$courierCompany)) {
            $this->addError($attribute, '<b>[' . $barcode . ']</b> ' . Yii::t('outbound/errors', 'Этот заказ уже отсканирован в другой отгрузочный лист'));
            return;
        }

        if ($this->validation->isExistPackageBarcode($title,$barcode,$courierCompany)) {
            $this->addError($attribute, '<b>[' . $barcode . ']</b> ' . Yii::t('outbound/errors', 'Этот шк уже отсканирован'));
            return;
        }

        if (!$this->validation->isOrderPackaged($barcode)) {
            $this->addError($attribute, '<b>[' . $barcode . ']</b> ' . Yii::t('outbound/errors', 'Этот заказ еще не собран'));
            return;
        }

        if (!$this->validation->isOrderFromOtherCourierCompany($this->getDTO())) {
            $this->addError($attribute, '<b>[' . $barcode . ']</b> ' . Yii::t('outbound/errors', 'Этот заказ для другой курьерсокой компании'));
            return;
        }
    }

    /*
    *
    * */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('outbound/forms', 'Название листа отгрузки'),
            'barcode' => Yii::t('outbound/forms', 'ШК места'),
            'courierCompany' => Yii::t('outbound/forms', 'Курьерская компания'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->title = $this->title;
        $dto->barcode = $this->barcode;
        $dto->courierCompany = $this->courierCompany;
        return $dto;
    }
}