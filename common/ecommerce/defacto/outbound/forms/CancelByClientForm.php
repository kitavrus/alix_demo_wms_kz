<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\outbound\forms;

use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use common\ecommerce\defacto\outbound\repository\CancelOutboundByClientRepository;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;
use common\ecommerce\defacto\outbound\service\OutboundService;
use  common\ecommerce\defacto\outbound\validation\ValidationOutbound;
use common\ecommerce\entities\EcommerceStock;
use yii\base\Model;
use Yii;

class CancelByClientForm extends Model
{
    public $cancelKey;
    public $outboundOrderNumber;
    public $boxAddress;

    private $service;
    private $repository;
    private $barcodeService;
    private $outboundRepository;

    const SCENARIO_OUTBOUND_ORDER_NUMBER = 'OUTBOUND-ORDER-NUMBER';
    const SCENARIO_BOX_ADDRESS = 'BOX-ADDRESS';
    const SCENARIO_SHOW_ORDER_ITEMS = 'SHOW-ORDER-ITEMS';
    const SCENARIO_SHOW_ALL_ORDER_ITEMS = 'SHOW-ALL-ORDER-ITEMS';
    const SCENARIO_EMPTY_BOX = 'EMPTY-BOX';
    const SCENARIO_CANCEL_DONE = 'CANCEL-DONE';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->cancelKey = date('d-m-Y');
        $this->service = new OutboundService();
        $this->barcodeService = new BarcodeService();
        $this->outboundRepository = new OutboundRepository();
        $this->repository = new CancelOutboundByClientRepository();
    }

    /*
     * */
    public function rules()
    {
        return [
            // SCAN OUTBOUND ORDER NUMBER
            [['cancelKey'], 'trim', 'on' => self::SCENARIO_OUTBOUND_ORDER_NUMBER],
            [['cancelKey'], 'string', 'on' => self::SCENARIO_OUTBOUND_ORDER_NUMBER],
            [['cancelKey'], 'required', 'on' => self::SCENARIO_OUTBOUND_ORDER_NUMBER],

            [['outboundOrderNumber'], 'trim', 'on' => self::SCENARIO_OUTBOUND_ORDER_NUMBER],
            [['outboundOrderNumber'], 'string', 'on' => self::SCENARIO_OUTBOUND_ORDER_NUMBER],
            [['outboundOrderNumber'], 'OutboundOrderNumber', 'on' => self::SCENARIO_OUTBOUND_ORDER_NUMBER],
            [['outboundOrderNumber'], 'required', 'on' => self::SCENARIO_OUTBOUND_ORDER_NUMBER],

            // SCAN ADDRESS BOX
            [['cancelKey'], 'trim', 'on' => self::SCENARIO_BOX_ADDRESS],
            [['cancelKey'], 'string', 'on' => self::SCENARIO_BOX_ADDRESS],
            [['cancelKey'], 'required', 'on' => self::SCENARIO_BOX_ADDRESS],

            [['outboundOrderNumber'], 'trim', 'on' => self::SCENARIO_BOX_ADDRESS],
            [['outboundOrderNumber'], 'string', 'on' => self::SCENARIO_BOX_ADDRESS],
            [['outboundOrderNumber'], 'OutboundOrderNumber', 'on' => self::SCENARIO_BOX_ADDRESS],
            [['outboundOrderNumber'], 'required', 'on' => self::SCENARIO_BOX_ADDRESS],

            [['boxAddress'], 'trim', 'on' => self::SCENARIO_BOX_ADDRESS],
            [['boxAddress'], 'string', 'on' => self::SCENARIO_BOX_ADDRESS],
            [['boxAddress'], 'BoxAddress', 'on' => self::SCENARIO_BOX_ADDRESS],
            [['boxAddress'], 'required', 'on' => self::SCENARIO_BOX_ADDRESS],
            // SHOW ORDER ITEMS
            [['outboundOrderNumber'], 'trim', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],
            [['outboundOrderNumber'], 'string', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],
            [['outboundOrderNumber'], 'OutboundOrderNumber', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],
            [['outboundOrderNumber'], 'required', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],
            // SHOW ALL ORDER ITEMS
            [['cancelKey'], 'trim', 'on' => self::SCENARIO_SHOW_ALL_ORDER_ITEMS],
            [['cancelKey'], 'string', 'on' => self::SCENARIO_SHOW_ALL_ORDER_ITEMS],
            [['cancelKey'], 'cancelKey', 'on' => self::SCENARIO_SHOW_ALL_ORDER_ITEMS],
            [['cancelKey'], 'required', 'on' => self::SCENARIO_SHOW_ALL_ORDER_ITEMS],
            // EMPTY BOX
            [['cancelKey'], 'required', 'on' => self::SCENARIO_EMPTY_BOX],

            [['boxAddress'], 'trim', 'on' => self::SCENARIO_EMPTY_BOX],
            [['boxAddress'], 'string', 'on' => self::SCENARIO_EMPTY_BOX],
            [['boxAddress'], 'BoxAddress', 'on' => self::SCENARIO_EMPTY_BOX],
            [['boxAddress'], 'required', 'on' => self::SCENARIO_EMPTY_BOX],

            // CANCEL ALL SCANNED ORDER
            [['cancelKey'], 'required', 'on' => self::SCENARIO_CANCEL_DONE],
            [['cancelKey'], 'ifCancelScannedOrder', 'on' => self::SCENARIO_CANCEL_DONE],

        ];
    }

    public function getOrderByAny($aOutboundOrderNumber)
    {
        $outbound = $this->outboundRepository->getOrderByAny($aOutboundOrderNumber);
        if($outbound) {
            return $outbound->order_number;
        }
        return $aOutboundOrderNumber;
    }

    public function OutboundOrderNumber($attribute, $params)
    {
        $value = $this->getOrderByAny($this->outboundOrderNumber);
        if (!$this->service->isOrderExist($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели несуществующий номер заказа'));
        }

        if ($this->repository->isDoneOrder($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Этот заказ уже отменен'));
        }
    }

    public function cancelKey($attribute, $params)
    {
//        $value = $this->$attribute;
//        if (!$this->service->isOrderExist($value)) {
//            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели несуществующий номер заказа'));
//        }
    }

    public function BoxAddress($attribute, $params)
    {
        $value = $this->$attribute;
        if (!$this->barcodeService->isOurInboundBoxBarcode($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД короба'));
        }
    }
    
    public function ifCancelScannedOrder($attribute, $params)
    {
        $cancelKey = $this->cancelKey;
        if ($this->repository->isExistOrderWithoutBoxAddress($cancelKey)) {
            $this->addError($attribute, '<b>[' . $cancelKey . ']</b> ' . Yii::t('outbound/errors', 'Вы не всем заказам указали короб'));
        }

        if ($this->repository->isCountScannedOrderNotZero($cancelKey)) {
            $this->addError($attribute, '<b>[' . $cancelKey . ']</b> ' . Yii::t('outbound/errors', 'Вы не отсканировали ни один товар или заказ'));
        }
    }

    /*
    *
    * */
    public function attributeLabels()
    {
        return [
            'outboundOrderNumber' => Yii::t('outbound/forms', 'Номер заказа'),
            'boxAddress' => Yii::t('outbound/forms', 'box Address'),
            'productAddress' => Yii::t('outbound/forms', 'product Address'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->cancelKey = $this->cancelKey;
        $dto->outboundOrderNumber = $this->getOrderByAny($this->outboundOrderNumber);
        $dto->boxAddress = $this->boxAddress;

        return $dto;
    }
}