<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\transfer\forms;

use common\ecommerce\defacto\transfer\service\TransferService;
use common\ecommerce\defacto\transfer\validation\TransferValidation;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

class TransferForm extends Model
{
    const SCENARIO_PIKING_LIST_BARCODE = 'PIKING-LIST-BARCODE';
    const SCENARIO_OUR_BOX_BARCODE = 'OUR-BOX-BARCODE';
    const SCENARIO_LC_BOX_BARCODE = 'LC-BARCODE';
    const SCENARIO_MOVE_ALL_BOX_BARCODE = 'MOVE-ALL-LC-BARCODE';
    const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';
    const SCENARIO_EMPTY_BOX = 'EMPTY-BOX';
    const SCENARIO_SHOW_BOX_ITEMS = 'SHOW-BOX-ITEMS';
    const SCENARIO_SHOW_LC_BOX_ITEMS = 'SHOW-LC-BOX-ITEMS';
    const SCENARIO_SHOW_SCANNED_ITEMS = 'SHOW-SCANNED-ITEMS';
    const SCENARIO_SHOW_ORDER_ITEMS = 'SHOW-ORDER-ITEMS';
    const SCENARIO_COMPLETE_ORDER = 'COMPLETE-ORDER';

    public $pickingListBarcode;
    public $ourBoxBarcode;
    public $lcBarcode;
    public $productBarcode;

    private $check;
    private $service;


    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->check = new TransferValidation();
        $this->service = new TransferService();
    }

    /**
     *
     * */
    public function rules()
    {
        return [
            [['pickingListBarcode'], 'trim', 'on' => self::SCENARIO_PIKING_LIST_BARCODE],
            [['pickingListBarcode'], 'string', 'on' => self::SCENARIO_PIKING_LIST_BARCODE],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_PIKING_LIST_BARCODE],
            [['pickingListBarcode'], 'PickingListBarcode', 'on' => self::SCENARIO_PIKING_LIST_BARCODE],

            [['ourBoxBarcode'], 'trim', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['ourBoxBarcode'], 'string', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['ourBoxBarcode'], 'required', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['pickingListBarcode'], 'PickingListBarcode', 'on' => self::SCENARIO_OUR_BOX_BARCODE],
            [['ourBoxBarcode'], 'OurBoxBarcode', 'on' => self::SCENARIO_OUR_BOX_BARCODE],

            [['lcBarcode'], 'trim', 'on' => self::SCENARIO_LC_BOX_BARCODE],
            [['lcBarcode'], 'string', 'on' => self::SCENARIO_LC_BOX_BARCODE],
            [['lcBarcode'], 'required', 'on' => self::SCENARIO_LC_BOX_BARCODE],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_LC_BOX_BARCODE],
            [['ourBoxBarcode'], 'required', 'on' => self::SCENARIO_LC_BOX_BARCODE],
            [['lcBarcode'], 'LCBarcode', 'on' => self::SCENARIO_LC_BOX_BARCODE],

            [['lcBarcode'], 'trim', 'on' => self::SCENARIO_MOVE_ALL_BOX_BARCODE],
            [['lcBarcode'], 'string', 'on' => self::SCENARIO_MOVE_ALL_BOX_BARCODE],
            [['lcBarcode'], 'required', 'on' => self::SCENARIO_MOVE_ALL_BOX_BARCODE],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_MOVE_ALL_BOX_BARCODE],
            [['ourBoxBarcode'], 'required', 'on' => self::SCENARIO_MOVE_ALL_BOX_BARCODE],
            [['lcBarcode'], 'LCBarcode', 'on' => self::SCENARIO_MOVE_ALL_BOX_BARCODE],

            [['productBarcode'], 'trim', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'string', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['ourBoxBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['lcBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'ProductBarcode', 'on' => self::SCENARIO_PRODUCT_BARCODE],

            [['ourBoxBarcode'], 'trim', 'on' => self::SCENARIO_EMPTY_BOX],
            [['ourBoxBarcode'], 'string', 'on' => self::SCENARIO_EMPTY_BOX],
            [['ourBoxBarcode'], 'required', 'on' => self::SCENARIO_EMPTY_BOX],
            [['lcBarcode'], 'required', 'on' => self::SCENARIO_EMPTY_BOX],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_EMPTY_BOX],
            [['pickingListBarcode'], 'PickingListBarcode', 'on' => self::SCENARIO_EMPTY_BOX],
            [['ourBoxBarcode'], 'OurBoxBarcode', 'on' => self::SCENARIO_EMPTY_BOX],
            [['lcBarcode'], 'LCBarcode', 'on' => self::SCENARIO_EMPTY_BOX],

            [['ourBoxBarcode'], 'trim', 'on' => self::SCENARIO_SHOW_BOX_ITEMS],
            [['ourBoxBarcode'], 'string', 'on' => self::SCENARIO_SHOW_BOX_ITEMS],
            [['ourBoxBarcode'], 'required', 'on' => self::SCENARIO_SHOW_BOX_ITEMS],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_SHOW_BOX_ITEMS],

            [['lcBarcode'], 'trim', 'on' => self::SCENARIO_SHOW_LC_BOX_ITEMS],
            [['lcBarcode'], 'string', 'on' => self::SCENARIO_SHOW_LC_BOX_ITEMS],
            [['lcBarcode'], 'required', 'on' => self::SCENARIO_SHOW_LC_BOX_ITEMS],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_SHOW_LC_BOX_ITEMS],

            [['pickingListBarcode'], 'trim', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],
            [['pickingListBarcode'], 'string', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],

            [['pickingListBarcode'], 'trim', 'on' => self::SCENARIO_SHOW_SCANNED_ITEMS],
            [['pickingListBarcode'], 'string', 'on' => self::SCENARIO_SHOW_SCANNED_ITEMS],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_SHOW_SCANNED_ITEMS],

            [['pickingListBarcode'], 'trim', 'on' => self::SCENARIO_COMPLETE_ORDER],
            [['pickingListBarcode'], 'string', 'on' => self::SCENARIO_COMPLETE_ORDER],
            [['pickingListBarcode'], 'required', 'on' => self::SCENARIO_COMPLETE_ORDER],
        ];
    }

    /**
     * @param $attribute
     * @param array $params
     */
    public function PickingListBarcode($attribute, $params = [])
    {
        $pickingListBarcode = $this->pickingListBarcode;
//        $pickingListInfo = $this->parsePikingListBarcode($pickingListBarcode);
        if (!$this->check->isReadyPrintPickListForScanning($pickingListBarcode)) {
            $this->addError($attribute, '<b>[' . $pickingListBarcode . ']</b> ' . Yii::t('outbound/errors', 'Эт лист сборки еще не напечатан или не существует'));
        }
    }

    /**
     * @param $attribute
     * @param array $params
     */
    public function OurBoxBarcode($attribute,  $params = [])
    {
        $pickingListBarcode = $this->pickingListBarcode;
        $ourBoxBarcode = $this->ourBoxBarcode;
        if (!$this->check->isOurBoxBarcode($ourBoxBarcode)) {
            $this->addError($attribute, '<b>[' . $ourBoxBarcode . ']</b> ' . Yii::t('outbound/errors', 'Не верный шк короба'));
        }

        if (!$this->check->isProductOurBoxBarcodeExist($pickingListBarcode,$ourBoxBarcode)) {
            $this->addError($attribute, '<b>[' . $ourBoxBarcode . ']</b> ' . Yii::t('outbound/errors', 'Это шк короба нет в заказе'));
        }
    }

    /**
     * @param $attribute
     * @param array $params
     */
    public function LCBarcode($attribute,  $params = [])
    {
        $pickingListBarcode = $this->pickingListBarcode;
        $lcBarcode = $this->lcBarcode;
        if (!$this->check->isLcBarcode($lcBarcode)) {
            $this->addError($attribute, '<b>[' . $lcBarcode . ']</b> ' . Yii::t('outbound/errors', 'Это не штрих-код клиента короба'));
        }

//        if (!$this->check->isLcBarcodeExist($lcBarcode)) {
//            $this->addError($attribute, '<b>[' . $pickingListBarcode . ']</b> ' . Yii::t('outbound/errors', 'Это шк нет в системе'));
//        }
    }

    /**
     * @param $attribute
     * @param array $params
     */
    public function productBarcode($attribute,  $params = [])
    {
        $pickingListBarcode = $this->pickingListBarcode;
        $productBarcode = $this->productBarcode;
        $ourBoxBarcode = $this->ourBoxBarcode;
        if (!$this->check->isProductBarcode($productBarcode)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('outbound/errors', 'Это не штрих-код товара'));
        }

        if (!$this->check->isProductBarcodeExist($pickingListBarcode,$productBarcode)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('outbound/errors', 'Это шк нет в системе'));
        }

        if (!$this->check->isExtraBarcodeInOrder($pickingListBarcode,$productBarcode)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('outbound/errors', 'Это шк лишний в системе'));
        }
        // TODO Закоментировал для обработки старой версии
         if ($this->check->isExtraBarcodeInBox($ourBoxBarcode,$pickingListBarcode, $productBarcode)) {
          $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('outbound/errors', 'Это шк лишний в коробке'));
        }
    }

    /**
    *
    * */
    public function attributeLabels()
    {
        return [
            'pickingListBarcode' => Yii::t('outbound/forms', 'Шк листа сборки'),
            'ourBoxBarcode' => Yii::t('outbound/forms', 'Шк нашего короба'),
            'lcBarcode' => Yii::t('outbound/forms', 'Шк короба клиента'),
            'productBarcode' => Yii::t('outbound/forms', 'Шк товара'),
        ];
    }

    public function scannedPrintPickListBarcode() {
        return $this->service->scannedPrintPickListBarcode($this->getDTO());
    }

    public function scannedLcBarcode() {
        return $this->service->scannedLcBarcode($this->getDTO());
    }

    public function moveAllProductFromOurBox() {
        return $this->service->moveAllProductFromOurBox($this->getDTO());
    }

    public function scannedOurBoxBarcode() {
        return $this->service->scannedOurBoxBarcode($this->getDTO());
    }

    public function scannedProductBarcode() {
        return $this->service->scannedProductBarcode($this->getDTO());
    }

    public function showBoxItems() {
        return $this->service->showBoxItems($this->getDTO());
    }
    public function showLcBoxItems() {
        return $this->service->showLcBoxItems($this->getDTO());
    }

    public function showScannedItems() {
        return $this->service->showScannedItems($this->getDTO());
    }

    public function showOrderItems() {
        return $this->service->showOrderItems($this->getDTO());
    }

    public function emptyBox() {
        return $this->service->emptyBox($this->getDTO());
    }

    public function complete() {
        return $this->service->sendByAPI($this->getDTO());
    }

    /**
     * */
    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->pickingListBarcode = $this->pickingListBarcode;
        $dto->ourBoxBarcode = $this->ourBoxBarcode;
        $dto->lcBarcode = $this->lcBarcode;
        $dto->productBarcode = $this->productBarcode;
        return $dto;
    }

}