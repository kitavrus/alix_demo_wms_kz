<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\outbound\forms;

use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use common\ecommerce\defacto\outbound\service\OutboundService;
use  common\ecommerce\defacto\outbound\validation\ValidationOutbound;
use common\ecommerce\entities\EcommerceStock;
use yii\base\Model;
use Yii;

class CancelForm extends Model
{
    public $outboundOrderNumber;
    public $cancelReason;

    private $service;

    const SCENARIO_OUTBOUND_ORDER_NUMBER = 'OUTBOUND-ORDER-NUMBER';
    const SCENARIO_CANCEL_REASON = 'CANCEL-REASON';

    public function __construct($config = [])
    {
        parent::__construct($config);

//        $this->validation = new ValidationOutbound();
        $this->service = new OutboundService();
    }

    /*
     * */
    public function rules()
    {
        return [
            [['outboundOrderNumber'], 'trim'],
            [['outboundOrderNumber'], 'string'],
            // SCAN OUTBOUND ORDER NUMBER
            [['outboundOrderNumber'], 'OutboundOrderNumber', 'on' => self::SCENARIO_OUTBOUND_ORDER_NUMBER],
            [['outboundOrderNumber'], 'required', 'on' => self::SCENARIO_OUTBOUND_ORDER_NUMBER],
            // SCAN CANCEL REASON
            [['cancelReason'], 'CancelReason', 'on' => self::SCENARIO_CANCEL_REASON],
            [['cancelReason','outboundOrderNumber'], 'required', 'on' => self::SCENARIO_CANCEL_REASON],
        ];
    }

    public function OutboundOrderNumber($attribute, $params)
    {
        $value = $this->$attribute;
        if (!$this->service->isOrderExist($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели несуществующий номер заказа'));
        }
    }

    public function CancelReason($attribute, $params)
    {
        $value = $this->$attribute;
//        if (!EmployeeRepository::isEmployee($value)) {
//            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД СОТРУДНИКА'));
//        }
    }

    /*
    *
    * */
    public function attributeLabels()
    {
        return [
            'outboundOrderNumber' => Yii::t('outbound/forms', 'Номер заказа'),
            'cancelReason' => Yii::t('outbound/forms', 'Причина отказа'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->outboundOrderNumber = $this->outboundOrderNumber;
        $dto->cancelReason = $this->cancelReason;
        return $dto;
    }
}