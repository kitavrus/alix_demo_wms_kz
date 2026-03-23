<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 23.12.2016
 * Time: 11:44
 */

namespace stockDepartment\modules\wms\managers\defacto\api;


use common\components\BarcodeManager;
use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\stock\models\Stock;
use common\overloads\ArrayHelper;
use yii\helpers\Json;

class CrossDockItemService
{
    private $crossDock;
    private $productSerializeDataDTO;
    private $crossDockItemDTO;

    /**
     * CrossDockItemService constructor.
     * @param $crossDock
     * @param $productSerializeDataDTO
     * @param $crossDockItemDTO
     */
    public function __construct($crossDock, $productSerializeDataDTO, $crossDockItemDTO) {
        $this->crossDock = $crossDock;
        $this->productSerializeDataDTO = $productSerializeDataDTO;
        $this->crossDockItemDTO = $crossDockItemDTO;
    }

    /*
    *
    * */
    public function create() {
        $this->makeProductSerializeData();
        $this->addCrossDockItem();
        $this->updateCrossDock();

    }

    protected function updateCrossDock() {
        $this->crossDock->accepted_number_places_qty += $this->crossDockItemDTO->getNumberPlacesQty();
        $this->crossDock->box_m3 += $this->crossDockItemDTO->getBoxM3();
        $this->crossDock->save(false);
    }

    /*
    * @param $crossDockItemTDO \stockDepartment\modules\wms\managers\defacto\api\CrossDockItemDTO
    * */
    protected function addCrossDockItem() {
        $crossDockItem = new CrossDockItems();
        $crossDockItem->cross_dock_id = $this->crossDock->id;
        $crossDockItem->box_barcode = $this->crossDockItemDTO->getBoxBarcode();
        $crossDockItem->expected_number_places_qty = $this->crossDockItemDTO->getNumberPlacesQty();
        $crossDockItem->accepted_number_places_qty = $this->crossDockItemDTO->getNumberPlacesQty();
        $crossDockItem->box_m3 = $this->crossDockItemDTO->getBoxM3();
        $crossDockItem->weight_net = $this->crossDockItemDTO->getWeightNet();
        $crossDockItem->weight_brut = $this->crossDockItemDTO->getWeightBrut();
        $crossDockItem->product_serialize_data = $this->makeProductSerializeData();
//        $crossDockItem->product_serialize_data = $this->crossDockItemDTO->getProductSerializeData();
        $crossDockItem->field_extra1 = $this->crossDockItemDTO->getInBoundId(); // $item['field_extra1'];
        $crossDockItem->field_extra2 = $this->crossDockItemDTO->getLotBarcode(); //$item['field_extra2']; //
        $crossDockItem->field_extra3 = $this->crossDockItemDTO->getPartyNumber();//$cuOrder->party_number;
        $crossDockItem->status = Stock::STATUS_CROSS_DOCK_SCANNED;//$cuOrder->party_number;
        $crossDockItem->save(false);
    }

    /*
     * @return JSON
     * */
    protected function makeProductSerializeData() {
        return Json::encode(['extra_fields'=>
                ['apiLogValue'=> [
                    'Id'=>$this->productSerializeDataDTO->getId(),
                    'FromBusinessUnitId'=>$this->productSerializeDataDTO->getFromBusinessUnitId(),
                    'LcOrCartonLabel'=>$this->productSerializeDataDTO->getLcOrCartonLabel(),
                    'NumberOfCartons'=>$this->productSerializeDataDTO->getNumberOfCartons(),
                    'SkuId'=>$this->productSerializeDataDTO->getSkuId(),
                    'LotOrSingleBarcode'=>$this->productSerializeDataDTO->getLotOrSingleBarcode(),
                    'LotOrSingleQuantity'=>$this->productSerializeDataDTO->getLotOrSingleQuantity(),
                    'Status'=>$this->productSerializeDataDTO->getStatus(),
                    'AppointmentBarcode'=>$this->productSerializeDataDTO->getAppointmentBarcode(),
                    'ToBusinessUnitId'=>$this->productSerializeDataDTO->getToBusinessUnitId(),
                    'FlowType'=>$this->productSerializeDataDTO->getFlowType(),
                ]
            ]
        ]);
    }

    public static function makePartyNumber($crossDockPartyNumber) {
        return ltrim($crossDockPartyNumber,'2-');
    }
    /*
     * @param $crossDockBoxM3 Example: 32
     * */
    public static function getBoxM3($crossDockBoxM3) {
        $boxM3 = 0.096;
        if($keyBoxM3 = BarcodeManager::mapM3ToBoxSize($crossDockBoxM3)) {
            $boxM3 = BarcodeManager::getBoxM3($keyBoxM3);
        }
        return $boxM3;
    }

    /*
     * */
    public static function extractJsonData($jsonData) {
        $jsonProductData = [];
        if (!empty($jsonData)) {
            $jsonProductData = Json::decode($jsonData);
            if($extraFieldLet =  ArrayHelper::getValue($jsonProductData,'extra_fields')) {
                $jsonProductData = Json::decode($extraFieldLet);
            }
        }
        return $jsonProductData;
    }
}