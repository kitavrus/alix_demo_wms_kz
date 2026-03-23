<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.07.2017
 * Time: 8:00
 */

namespace stockDepartment\modules\wms\managers\miele;


use common\modules\stock\models\ConstantZone;
use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\managers\miele\Constants;
class DTO {
    public function instanceInbound()    { return new InboundDTO();    }
    public function instanceOutbound()   { return new OutboundDTO();   }
    public function instanceStock()      { return new StockDTO();      }
    public function instanceMasterData() { return new MasterDataDTO(); }
    public function instanceMovement()   { return new MovementDTO();   }

    public static function mapOurStatusToClient($status)
    {
        switch($status) {
            case Stock::STATUS_INBOUND_NEW :
                return Constants::STATUS_NEW;
                break;
            case Stock::STATUS_INBOUND_SCANNING :
            case Stock::STATUS_INBOUND_OVER_SCANNED :
            case Stock::STATUS_INBOUND_SCANNED :
            case Stock::STATUS_INBOUND_PLACED :
            case Stock::STATUS_INBOUND_SORTED :
            case Stock::STATUS_INBOUND_SORTING :
            case Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE :
            case Stock::STATUS_INBOUND_DONE :
                return Constants::STATUS_IN_WORKING;
                break;
            case Stock::STATUS_INBOUND_ACCEPTED :
                return Constants::STATUS_RESERVED;
                break;
            case Stock::STATUS_INBOUND_COMPLETE :
                return Constants::STATUS_COMPLETE;
                break;
            case Stock::STATUS_INBOUND_CANCEL :
                return Constants::STATUS_CANCEL;
                break;
        }

        return -1;
    }

    public static function mapClientStatusToOur($status) {

        switch($status) {
            case Constants::STATUS_NEW:
                return Stock::STATUS_INBOUND_NEW;
                break;
            case Constants::STATUS_IN_WORKING:
                return Stock::STATUS_INBOUND_SCANNING;
                break;
            case Constants::STATUS_RESERVED :
                return Stock::STATUS_INBOUND_ACCEPTED;
                break;
            case Constants::STATUS_COMPLETE :
                return Stock::STATUS_INBOUND_COMPLETE;
                break;
            case Constants::STATUS_CANCEL :
                return Stock::STATUS_INBOUND_CANCEL;
                break;
        }

        return -1;
    }

    public static function mapOurZoneToClient($zone) {
        switch ($zone) {
            case ConstantZone::CATEGORY_A:
                return Constants::ZONE_CATEGORY_A;
                break;
            case ConstantZone::CATEGORY_B:
                return Constants::ZONE_CATEGORY_B;
                break;
            case ConstantZone::CATEGORY_VV:
                return Constants::ZONE_CATEGORY_VV;
                break;
            case ConstantZone::CATEGORY_RETURN:
                return Constants::ZONE_RETURN;
                break;
            case ConstantZone::CATEGORY_FUNDS:
                return Constants::ZONE_FUNDS;
                break;
            case ConstantZone::CATEGORY_UNADAPTED:
                return Constants::ZONE_UNADAPTED;
                break;
        }

        return -1;
    }

    public static function mapClientZoneToOur($zone) {
        switch($zone) {
            case Constants::ZONE_CATEGORY_A:
                return ConstantZone::CATEGORY_A;
                break;
            case Constants::ZONE_CATEGORY_B:
                return ConstantZone::CATEGORY_B;
                break;
            case Constants::ZONE_CATEGORY_VV:
                return ConstantZone::CATEGORY_VV;
                break;
            case Constants::ZONE_RETURN:
                return ConstantZone::CATEGORY_RETURN;
                break;
            case Constants::ZONE_FUNDS:
                return ConstantZone::CATEGORY_FUNDS;
                break;
            case Constants::ZONE_UNADAPTED:
                return ConstantZone::CATEGORY_UNADAPTED;
                break;
        }

        return -1;
    }

    public static function mapOutboundOurStatusToClient($status)
    {
        switch($status) {
            case Stock::STATUS_OUTBOUND_NEW :
                return Constants::STATUS_NEW;
                break;
            case Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST :
            case Stock::STATUS_OUTBOUND_PICKING :
            case Stock::STATUS_OUTBOUND_PICKED :
            case Stock::STATUS_OUTBOUND_SCANNING :
            case Stock::STATUS_OUTBOUND_SCANNED :
            case Stock::STATUS_OUTBOUND_SORTING :
            case Stock::STATUS_OUTBOUND_SORTED :
            case Stock::STATUS_OUTBOUND_PACKING :
            case Stock::STATUS_OUTBOUND_PACKED :
            case Stock::STATUS_OUTBOUND_SHIPPING :
            case Stock::STATUS_OUTBOUND_SHIPPED :
                return Constants::STATUS_IN_WORKING;
                break;
            case Stock::STATUS_OUTBOUND_FULL_RESERVED :
                return Constants::STATUS_RESERVED;
                break;
            case Stock::STATUS_OUTBOUND_COMPLETE :
                return Constants::STATUS_COMPLETE;
                break;
            case Stock::STATUS_OUTBOUND_CANCEL :
                return Constants::STATUS_CANCEL;
                break;
        }

        return 0;
    }

    public static function mapOutboundClientStatusToOur($status) {

        switch($status) {
            case Constants::STATUS_NEW:
                return Stock::STATUS_OUTBOUND_NEW;
                break;
            case Constants::STATUS_IN_WORKING:
                return Stock::STATUS_OUTBOUND_SCANNING;
                break;
            case Constants::STATUS_RESERVED :
                return Stock::STATUS_OUTBOUND_FULL_RESERVED;
                break;
            case Constants::STATUS_COMPLETE :
                return Stock::STATUS_OUTBOUND_COMPLETE;
                break;
            case Constants::STATUS_CANCEL :
                return Stock::STATUS_OUTBOUND_CANCEL;
                break;
        }

        return 0;
    }
}