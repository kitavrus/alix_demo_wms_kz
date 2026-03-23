<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */
namespace common\ecommerce\defacto\outbound\service;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\OutboundPlaceAddressSorting;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;
use Yii;

class PartReReservedService
{
    private $repository;
    private $dto;

    public function __construct($dto = [])
    {
        $this->repository = new OutboundRepository();
        $this->dto = $dto;
    }

    public function getReservedProducts($outboundId) {
        return $this->repository->pickingList($outboundId);
    }

    public function findAvailableProductsInOtherAddress($stockId) {
        $stock = $this->repository->findStockById($stockId);
        return $this->repository->findAvailableProductsInOtherAddress($stock->product_barcode);
    }

    public function findStockById($stockId) {
        return $this->repository->findStockById($stockId);
    }

    public function getOrderByStockById($stockId) {
        return $this->repository->getOrderByStockById($stockId);
    }

    public function changeReservedAddress($newStockId, $oldStockId,$reReservedReason)
    {
        $order = $this->getOrderByStockById($oldStockId);
        $newStock = $this->findStockById($newStockId);
        $oldStock = $this->findStockById($oldStockId);

        $oldScanOutEmployeeId = $oldStock->scan_out_employee_id;
        $oldOutboundId = $oldStock->outbound_id;
        $oldOutboundItemId = $oldStock->outbound_item_id;
        $oldOutboundBox = $oldStock->outbound_box;
        $oldOutboundStatus = $oldStock->status_outbound;
        $oldStatusAvailability = $oldStock->status_availability;
        $oldScanOutDatetime = $oldStock->scan_out_datetime;

        $oldStock->scan_out_employee_id = $newStock->scan_out_employee_id;
        $oldStock->outbound_id = $newStock->outbound_id;
        $oldStock->outbound_item_id = $newStock->outbound_item_id;
        $oldStock->outbound_box = $newStock->outbound_box;
        $oldStock->status_outbound = $newStock->status_outbound;
        $oldStock->scan_out_datetime = $newStock->scan_out_datetime;

        $oldStock->reason_re_reserved = $reReservedReason;
        $oldStock->order_re_reserved = $order->order_number;
        $oldStock->place_address_sort1 = OutboundPlaceAddressSorting::NOT_FIND_OR_DAMAGE_PLACE_ADDRESS_SORT1;
        $oldStock->status_availability = StockAvailability::BLOCKED; //$newStock->status_availability;
		
        $oldStock->save(false);

        $newStock->scan_out_employee_id = $oldScanOutEmployeeId;
        $newStock->outbound_id = $oldOutboundId;
        $newStock->outbound_item_id = $oldOutboundItemId;
        $newStock->outbound_box = $oldOutboundBox;
        $newStock->status_outbound = $oldOutboundStatus;
        $newStock->status_availability = $oldStatusAvailability;
        $newStock->scan_out_datetime = $oldScanOutDatetime;
        $newStock->save(false);

        return '';
    }

    public function setReservedReason($stockId,$reReservedReason)
    {
        $order = $this->getOrderByStockById($stockId);
        $stock = $this->findStockById($stockId);

        $stock->reason_re_reserved = $reReservedReason;
        $stock->order_re_reserved = $order->order_number;
        $stock->save(false);

        return '';
    }
    public function showOtherProductAddresses($stockId,$changeReason)
    {
        $stock = $this->findStockById($stockId);
        $freeProducts = $this->findAvailableProductsInOtherAddress($stockId);

        if(empty($freeProducts)) {
            $this->setReservedReason($stockId,$changeReason);
        }

        $result = new \stdClass();
        $result->stock = $stock;
        $result->freeProducts = $freeProducts;
        $result->changeReason = $changeReason;

        return $result;
    }

}
