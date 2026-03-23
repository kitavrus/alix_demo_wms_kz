<?php

namespace stockDepartment\modules\kaspi\dto;


/**
 * DTO заказа; атрибуты + оболочка JSON:API (type, links, relationships) и покупатель из included.
 *
 * @property string|null $resourceType Тип ресурса JSON:API (обычно orders)
 * @property array|null $resourceLinks Блок links ресурса (напр. self)
 * @property array|null $relationships Связи entries, user (links, data)
 * @property CustomerDto|null $customerIncluded Покупатель из included[] по user (если был include)
 * @property string $id Идентификатор заказа
 * @property string $code Код заказа
 * @property CustomerDto $customer ФИО и номер телефона покупателя
 * @property int $totalPrice Общая сумма заказа в тенге
 * @property string $deliveryMode Способ доставки (код Kaspi)
 * @property bool|null $isKaspiDelivery Доставка через Kaspi (см. доку к deliveryMode)
 * @property string $paymentMode Способ оплаты
 * @property bool|null $signatureRequired Нужно ли подписать кредит
 * @property int|string|null $creditTerm Срок «Кредит на Покупки»
 * @property string $state Состояние заказа
 * @property int $creationDate Дата создания (миллисекунды)
 * @property int|null $approvedByBankDate Дата одобрения банком (миллисекунды)
 * @property string $status Статус заказа
 * @property bool|null $isImeiRequired Нужен ли IMEI по заказу
 * @property int $deliveryCost Стоимость доставки в тенге
 * @property bool $preOrder Оформлен по предзаказу (да/нет)
 * @property string $plannedDeliveryDate Планируемая дата доставки (миллисекунды)
 * @property string $reservationDate Дата доставки по предзаказу (миллисекунды)
 * @property string $waybill Ссылка на накладную
 * @property string $courierTransmissionPlanningDate Планируемая дата передачи заказа курьеру (миллисекунды)
 * @property string $courierTransmissionDate Фактическая дата передачи заказа курьеру (миллисекунды)
 * @property string $deliveryAddress Адрес доставки
 * @property string $waybillNumber Номер накладной
 * @property string|null $category Категория товара
 * @property string $deliveryCostForSeller Стоимость доставки для продавца в тенге
 * @property bool $express Покупатель выбрал Express-доставку (да/нет)
 * @property bool $returnedToWarehouse Заказ возвращен в пункт приема (да/нет)
 * @property string $entries Состав заказа
 * @property string $user Покупатель
 */
class OrderDto
{
    /** @var string|null */
    public $resourceType;
    /** @var array|null */
    public $resourceLinks;
    /** @var array|null */
    public $relationships;
    /** @var CustomerDto|null */
    public $customerIncluded;

    public $id;
    public $code;
    public $customer;
    public $totalPrice;
    public $deliveryMode;
    public $isKaspiDelivery;
    public $paymentMode;
    public $signatureRequired;
    public $creditTerm;
    public $state;
    public $creationDate;
    public $approvedByBankDate;
    public $status;
    public $isImeiRequired;
    public $deliveryCost;
    public $preOrder;
    public $plannedDeliveryDate;
    public $reservationDate;
    public $waybill;
    public $courierTransmissionPlanningDate;
    public $courierTransmissionDate;
    public $deliveryAddress;
    public $waybillNumber;
    public $category;
    public $deliveryCostForSeller;
    public $express;
    public $returnedToWarehouse;
    public $entries;
    public $user;

    /**
     * OrderDto constructor.
     *
     * @param string $id
     * @param string $code
     * @param CustomerDto $customer
     * @param int $totalPrice
     * @param string $deliveryMode
     * @param string $paymentMode
     * @param string $state
     * @param int $creationDate
     * @param string $status
     * @param int $deliveryCost
     * @param bool|null $isKaspiDelivery
     * @param bool|null $signatureRequired
     * @param int|string|null $creditTerm
     * @param int|null $approvedByBankDate
     * @param bool|null $isImeiRequired
     * @param string|null $category
     * @param bool|null $preOrder
     * @param string|null $plannedDeliveryDate
     * @param string|null $reservationDate
     * @param string|null $waybill
     * @param string|null $courierTransmissionPlanningDate
     * @param string|null $courierTransmissionDate
     * @param string|null $deliveryAddress
     * @param string|null $waybillNumber
     * @param string|null $deliveryCostForSeller
     * @param bool|null $express
     * @param bool|null $returnedToWarehouse
     * @param string|null $entries
     * @param string|null $user
     */
    public function __construct(
        $id,
        $code,
        CustomerDto $customer,
        $totalPrice,
        $deliveryMode,
        $paymentMode,
        $state,
        $creationDate,
        $status,
        $deliveryCost,
        $isKaspiDelivery = null,
        $signatureRequired = null,
        $creditTerm = null,
        $approvedByBankDate = null,
        $isImeiRequired = null,
        $category = null,
        $preOrder = null,
        $plannedDeliveryDate = null,
        $reservationDate = null,
        $waybill = null,
        $courierTransmissionPlanningDate = null,
        $courierTransmissionDate = null,
        $deliveryAddress = null,
        $waybillNumber = null,
        $deliveryCostForSeller = null,
        $express = null,
        $returnedToWarehouse = null,
        $entries = null,
        $user = null
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->customer = $customer;
        $this->totalPrice = $totalPrice;
        $this->deliveryMode = $deliveryMode;
        $this->isKaspiDelivery = $isKaspiDelivery !== null ? (bool) $isKaspiDelivery : null;
        $this->paymentMode = $paymentMode;
        $this->signatureRequired = $signatureRequired !== null ? (bool) $signatureRequired : null;
        $this->creditTerm = $creditTerm;
        $this->state = $state;
        $this->creationDate = (int) $creationDate;
        $this->approvedByBankDate = $approvedByBankDate !== null ? (int) $approvedByBankDate : null;
        $this->status = $status;
        $this->isImeiRequired = $isImeiRequired !== null ? (bool) $isImeiRequired : null;
        $this->deliveryCost = (int) $deliveryCost;
        $this->category = $category;
        $this->preOrder = $preOrder !== null ? (bool) $preOrder : null;
        $this->plannedDeliveryDate = $plannedDeliveryDate;
        $this->reservationDate = $reservationDate;
        $this->waybill = $waybill;
        $this->courierTransmissionPlanningDate = $courierTransmissionPlanningDate;
        $this->courierTransmissionDate = $courierTransmissionDate;
        $this->deliveryAddress = $deliveryAddress;
        $this->waybillNumber = $waybillNumber;
        $this->deliveryCostForSeller = $deliveryCostForSeller;
        $this->express = $express !== null ? (bool) $express : null;
        $this->returnedToWarehouse = $returnedToWarehouse !== null ? (bool) $returnedToWarehouse : null;
        $this->entries = $entries;
        $this->user = $user;

        $this->resourceType = null;
        $this->resourceLinks = null;
        $this->relationships = null;
        $this->customerIncluded = null;
    }

    /**
     * Преобразует объект в массив для API
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => $this->resourceType,
            'links' => $this->resourceLinks,
            'relationships' => $this->relationships,
            'customerIncluded' => $this->customerIncluded ? $this->customerIncluded->toArray() : null,
            'id' => $this->id,
            'code' => $this->code,
            'customer' => $this->customer ? $this->customer->toArray() : null,
            'totalPrice' => $this->totalPrice,
            'deliveryMode' => $this->deliveryMode,
            'isKaspiDelivery' => $this->isKaspiDelivery,
            'paymentMode' => $this->paymentMode,
            'signatureRequired' => $this->signatureRequired,
            'creditTerm' => $this->creditTerm,
            'state' => $this->state,
            'creationDate' => $this->creationDate,
            'approvedByBankDate' => $this->approvedByBankDate,
            'status' => $this->status,
            'isImeiRequired' => $this->isImeiRequired,
            'deliveryCost' => $this->deliveryCost,
            'category' => $this->category,
            'preOrder' => $this->preOrder,
            'plannedDeliveryDate' => $this->plannedDeliveryDate,
            'reservationDate' => $this->reservationDate,
            'waybill' => $this->waybill,
            'courierTransmissionPlanningDate' => $this->courierTransmissionPlanningDate,
            'courierTransmissionDate' => $this->courierTransmissionDate,
            'deliveryAddress' => $this->deliveryAddress,
            'waybillNumber' => $this->waybillNumber,
            'deliveryCostForSeller' => $this->deliveryCostForSeller,
            'express' => $this->express,
            'returnedToWarehouse' => $this->returnedToWarehouse,
            'entries' => $this->entries,
            'user' => $this->user,
        ];
    }
}

