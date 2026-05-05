<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\mapper;

use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceInboundItem;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\constants\StockInboundStatus;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\add_order\AddOrderItemResponseDTO;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\add_order\AddOrderResponseDTO;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\inbound_complete\InboundCompleteItemDTO;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\inbound_complete\InboundCompleteRequestDTO;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\OrderInfoDTO;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\status_order\StatusOrderResponseDTO;

class InboundAPIMapper
{
    /**
     * @param OrderInfoDTO $orderInfo
     * @return AddOrderResponseDTO
     */
    public function makeByItemAddOrderResponseDTO($orderInfo)
    {
        $r = new AddOrderResponseDTO();
        $r->orderNumber = $orderInfo->order->order_number;
        foreach ($orderInfo->items as $item) {
            $dtoItem = new AddOrderItemResponseDTO();
            $dtoItem->quantity = $item->product_expected_qty;
            $dtoItem->article = $item->product_model;
            $r->items[] = $dtoItem;
        }
        return $r;
    }

    /**
     * @param OrderInfoDTO $orderInfo
     * @return AddOrderResponseDTO
     */
    public function makeByItemAcceptedAddOrderResponseDTO($orderInfo)
    {
        $r = new AddOrderResponseDTO();
        $r->orderNumber = $orderInfo->order->order_number;
        foreach ($orderInfo->items as $item) {
            for ($i = 1; $i <= $item->product_expected_qty; $i++) {
                $dtoItem = new AddOrderItemResponseDTO();
                $dtoItem->name = $item->product_name;
                $dtoItem->barcode = $item->product_barcode;
                $dtoItem->brand = $item->product_brand;
                $dtoItem->color = $item->product_color;
                $dtoItem->quantity = $item->product_accepted_qty - $i < 0 ? 0 : 1;
                $dtoItem->article = $item->product_model;
                $r->items[] = $dtoItem;
            }
        }
        return $r;
    }

    /**
     * @param OrderInfoDTO $orderInfo
     * @return AddOrderResponseDTO
     */
    public function makeByStockAddOrderResponseDTO($orderInfo)
    {
        $r = new AddOrderResponseDTO();
        $r->orderNumber = $orderInfo->order->order_number;
        foreach ($orderInfo->stocks as $stock) {
            $i = new AddOrderItemResponseDTO();
            $i->name = empty($stock->product_name) ? "" : $stock->product_name;
            $i->barcode = $stock->product_barcode;
            $i->brand = $stock->product_brand;
            $i->color = $stock->product_color;
            $i->quantity = $stock->status_outbound == StockInboundStatus::SCANNED ? 1 : 0;
            $i->datamatrix = $stock->product_qrcode;
            $i->article = empty($stock->product_model) ? "" : $stock->product_model;
            $r->items[] = $i;
        }
        return $r;
    }

    /**
     * @param EcommerceInbound $order
     * @return StatusOrderResponseDTO
     */
    public function makeByOrderStatusOrderResponseDTO($order)
    {
        $r = new StatusOrderResponseDTO();
        $r->orderNumber = $order->order_number;
        $r->wmsId = $order->id;
        return $r;
    }

    /**
     * @param EcommerceInbound $order
     * @param array[] $items
     * @return StatusOrderResponseDTO
     */
    public function makeByOrderStatusWithDataOrderResponseDTO($order, $items)
    {
        $products = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                $products[] = [
                    "quantity" => $item["productQty"],
                    "guid" => $item["product_sku"],
                ];
            }
        }

        $r = new StatusOrderResponseDTO();
        $r->orderNumber = $order->order_number;
        $r->wmsId = $order->id;
        $r->items = $products;
        return $r;
    }

    /**
     * Сборка пэйлоада для POST /hs/NMDX/InboundComplete — фактическое
     * проведение прихода в 1С после нажатия "Принять" на ecommerce-приёмке.
     *
     * Поля позиции маппятся напрямую из ecommerce_inbound_items без обращения
     * к product_v2 — guid (=product_sku) и article (=product_model) уже сохранены
     * в строке заказа на этапе создания приёмки.
     *
     * @param EcommerceInbound       $order
     * @param EcommerceInboundItem[] $items
     * @param string                 $status InboundAPIStatus::COMPLETED | COMPLETED_WITH_DIFFERENCES
     * @return InboundCompleteRequestDTO
     */
    public function makeInboundCompleteRequestDTO($order, $items, $status)
    {
        $r = new InboundCompleteRequestDTO();
        $r->wmsId = strval($order->id);
        $r->orderNumber = $order->order_number;
        $r->status = $status;

        foreach ($items as $item) {
            $dtoItem = new InboundCompleteItemDTO();
            $dtoItem->guid = strval($item->product_sku);
            $dtoItem->article = strval($item->product_model);
            $dtoItem->barcode = strval($item->product_barcode);
            $dtoItem->quantity = (int) $item->product_accepted_qty;
            $r->items[] = $dtoItem;
        }

        return $r;
    }
}
