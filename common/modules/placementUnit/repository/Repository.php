<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.10.2017
 * Time: 17:19
 */

namespace common\modules\placementUnit\repository;


use common\modules\placementUnit\models\PlacementUnitFlow;
use common\modules\placementUnit\models\PlacementUnit;

class Repository
{
    public function create($dto){}
    public function isFree($barcode) {
        return $this->isStatus($barcode,Constant::STATUS_FREE);
    }
    public function isWork($barcode) {
        return $this->isStatus($barcode,Constant::STATUS_WORK);
    }

    public function isNotEmptyUnitFlow($barcode) {
        return PlacementUnitFlow::find()
            ->andWhere(['placement_unit_barcode'=>$barcode])
            ->andWhere(['status'=>Constant::STATUS_WORK])
            ->count() > 0;
    }


    public function isClose($barcode) {
        return $this->isStatus($barcode,Constant::STATUS_CLOSE);
    }
    public function isExist($barcode) {
        return PlacementUnit::find()->andWhere(['barcode'=>$barcode])->exists();
    }

    public function isWorkWithOrder($barcode,$inboundOrderID) {
        return PlacementUnitFlow::find()
            ->andWhere(['placement_unit_barcode'=>$barcode,'inbound_order_id'=>$inboundOrderID,'status'=>Constant::STATUS_WORK])
            ->exists();
    }

    public function createFlow($dto,$stockId)
    {
        $placementUnitFlow = new PlacementUnitFlow();
        $placementUnitFlow->client_id = $dto->clientId;
        $placementUnitFlow->stock_id = $stockId;
        $placementUnitFlow->inbound_order_id = $dto->orderNumberId;
        $placementUnitFlow->placement_unit_barcode = $dto->transportedBoxBarcode;
        $placementUnitFlow->product_barcode = $dto->productBarcode;
//        $placementUnitFlow->product_model = $dto->productModel;
        $placementUnitFlow->status = Constant::STATUS_WORK;
        $placementUnitFlow->save(false);
        return $placementUnitFlow;
    }


    public function setStatusFree($barcode) {
        return PlacementUnit::updateAll(['status'=>Constant::STATUS_FREE],['barcode'=>$barcode,'status'=>Constant::STATUS_WORK]);
    }

    public function setStatusWork($barcode) {
        return PlacementUnit::updateAll(['status'=>Constant::STATUS_WORK],['barcode'=>$barcode,'status'=>Constant::STATUS_FREE]);
    }

    public function setStatusClose($barcode) {
        return PlacementUnit::updateAll(['status'=>Constant::STATUS_CLOSE],['barcode'=>$barcode,'status'=>Constant::STATUS_WORK]);
    }

    public function setStatusCloseFlow($barcode,$inboundOrderId) {
        return PlacementUnitFlow::updateAll(['status'=>Constant::STATUS_CLOSE],['placement_unit_barcode'=>$barcode,'inbound_order_id'=>$inboundOrderId,'status'=>Constant::STATUS_WORK]);
    }

    public function getQtyInUnitByBarcode($barcode,$inboundOrderId) {
        return PlacementUnitFlow::find()->andWhere([
            'placement_unit_barcode'=>$barcode,
            'inbound_order_id'=>$inboundOrderId,
            'status'=>Constant::STATUS_WORK
        ])->count();
    }

    public function removeWorkUnitFlowItemsByBarcode($barcode,$inboundOrderID)
    {
        $this->setStatusFree($barcode);
        PlacementUnitFlow::deleteAll([
            'placement_unit_barcode' => $barcode,
            'inbound_order_id' => $inboundOrderID,
            'status' => Constant::STATUS_WORK
        ]);
    }

    public function getStocksIds($barcode,$inboundOrderID) {
        return PlacementUnitFlow::find()->select('stock_id')->andWhere([
            'placement_unit_barcode' => $barcode,
            'inbound_order_id' => $inboundOrderID,
            'status' => Constant::STATUS_WORK
        ])->column();
    }

    public function getStocksIdsByBarcode($barcode) {
        return PlacementUnitFlow::find()->select('stock_id')->andWhere([
            'placement_unit_barcode' => $barcode,
            'status' => Constant::STATUS_WORK
        ])->column();
    }

    private function isStatus($barcode,$status) {
        return PlacementUnit::find()->andWhere(['barcode'=>$barcode,'status'=>$status])->exists();
    }


}