<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 30.08.2017
 * Time: 20:53
 */

namespace stockDepartment\modules\wms\models\defacto\PickList\repository;


use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;

class PickListRepository
{
    private $clientID;
    /**
     * PickListRepository constructor.
     */
    public function __construct()
    {
        $this->clientID = Client::CLIENT_DEFACTO;
    }

    public static function isNew($pickListBarcode) {
        return OutboundPickingLists::find()->andWhere(['barcode'=>$pickListBarcode,'client_id'=>Client::CLIENT_DEFACTO,'status_scan'=>PickListConstants::STATUS_SCAN_NEW])->exists();
    }
    public static function isInProcess($pickListBarcode) {
        return OutboundPickingLists::find()->andWhere(['barcode'=>$pickListBarcode,'client_id'=>Client::CLIENT_DEFACTO,'status_scan'=>PickListConstants::STATUS_SCAN_IN_PROCESS])->exists();
    }
    public static function isDone($pickListBarcode) {
        return OutboundPickingLists::find()->andWhere(['barcode'=>$pickListBarcode,'client_id'=>Client::CLIENT_DEFACTO,'status_scan'=>PickListConstants::STATUS_SCAN_DONE])->exists();
    }
    public static function existPickList($pickListBarcode) {
        return OutboundPickingLists::find()->andWhere(['client_id'=>Client::CLIENT_DEFACTO,'barcode'=>$pickListBarcode])->exists();
    }
    public static function addPickListStatusScanNew($pickListBarcode) {
        return OutboundPickingLists::updateAll(['status_scan'=>PickListConstants::STATUS_SCAN_NEW],['barcode'=>$pickListBarcode,'client_id'=>Client::CLIENT_DEFACTO]);
    }
    public static function addPickListStatusScanInProcess($pickListBarcode) {
        return OutboundPickingLists::updateAll(['status_scan'=>PickListConstants::STATUS_SCAN_IN_PROCESS],['barcode'=>$pickListBarcode,'client_id'=>Client::CLIENT_DEFACTO]);
    }
    public static function addPickListStatusScanDone($pickListBarcode) {
        return OutboundPickingLists::updateAll(['status_scan'=>PickListConstants::STATUS_SCAN_DONE],['barcode'=>$pickListBarcode,'client_id'=>Client::CLIENT_DEFACTO]);
    }

    public static function addStockStatusScanned($pickListBarcode,$lotBarcode) {
        return Stock::updateAll([
            'pick_list_status'=>PickListConstants::STOCK_SCAN_STATUS_YES
        ],[
            'outbound_picking_list_barcode'=>$pickListBarcode,
            'product_barcode'=>$lotBarcode,
            'pick_list_status'=>PickListConstants::STOCK_SCAN_STATUS_NO,
            'client_id'=>Client::CLIENT_DEFACTO
        ]);
    }

    public static function existLotBarcode($pickListBarcode,$lotBarcode) {
        return Stock::find()->andWhere([
            'outbound_picking_list_barcode'=>$pickListBarcode,
            'product_barcode'=>$lotBarcode,
            'pick_list_status'=>PickListConstants::STOCK_SCAN_STATUS_NO,
            'client_id'=>Client::CLIENT_DEFACTO
        ])->exists();
    }
}