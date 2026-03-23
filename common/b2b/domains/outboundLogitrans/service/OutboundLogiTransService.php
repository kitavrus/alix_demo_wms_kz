<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.08.2020
 * Time: 8:09
 */

namespace common\b2b\domains\outboundLogitrans\service;


use common\b2b\domains\outbound\entities\OutboundBoxList;
use common\b2b\domains\outbound\repository\CargoDeliveryRepository;
use common\b2b\domains\outboundLogitrans\repository\OutboundLogiTransRepository;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\outbound\service\OutboundBoxService;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\components\TLHelper;
use yii\helpers\ArrayHelper;

class OutboundLogiTransService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new OutboundLogiTransRepository();
    }

    public function getActiveParentOrderNumberByClientId($clientID) {
        return $this->repository->getActiveParentOrderNumberByClientId($clientID);
    }

    public function getPickingListReadyForScanned() {

        $list = $this->repository->getPickingListReadyForScanned();

        return ArrayHelper::map($list,function($row) {
           $opl =  OutboundPickingLists::findOne(['outbound_order_id' => $row->id, 'status' => OutboundPickingLists::STATUS_END]);
            if($opl) {
                return $opl->barcode;
            }

            return '-1';
        },function($row) {

            $title = '';
            if ($to = $row->toPoint) {
                $title = $to->getPointTitleByPattern('{city_name} {shopping_center_name}');
                if (empty($to->shopping_center_name_lat)) {
                    $title = str_replace('/', '', $title);
                }
            }

            return $row->parent_order_number.'/'.$row->order_number.' / '.$title;
        });
    }
}