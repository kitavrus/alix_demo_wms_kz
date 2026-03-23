<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.08.2020
 * Time: 8:11
 */

namespace common\b2b\domains\outboundLogitrans\validation;


use common\b2b\domains\outbound\repository\CargoDeliveryRepository;
use common\modules\outbound\models\OutboundOrder;
use yii\helpers\ArrayHelper;

class OutboundLogiTransValidator
{
    private $repository;

    public function __construct() {
        $this->repository = new CargoDeliveryRepository();
    }

    public function isNotValidOrder($keyCargoDelivery,$outboundOrderId) {

        $response = new \stdClass();
        $response->isNotValid = false;
        $response->errorMessage = '';

        $outboundListInfo = $this->repository->getOutboundListInfo($outboundOrderId);

        if(!empty($outboundListInfo) && $outboundListInfo->status == 1) {
            $response->errorMessage = 'Этот заказ уже отгружен';
            $response->isNotValid = true;
            return $response;
        }

        return $response;
    }

    public function isNotValidScannedBoxBarcode($outboundOrderId,$boxBarcode) {

        $response = new \stdClass();
        $response->isNotValid = false;
        $response->errorMessage = '';

        $outboundListBoxInfo = $this->repository->getOutboundListBox($boxBarcode);

        if(!empty($outboundListBoxInfo) && $outboundListBoxInfo->our_outbound_id != $outboundOrderId) {
            $response->errorMessage = 'Этот короб из другого заказа: '.$outboundListBoxInfo->client_order_number;
            $response->isNotValid = true;
            return $response;
        }

        if(!empty($outboundListBoxInfo)) {
            $response->errorMessage = 'Этот короб уже отсканирован';
            $response->isNotValid = true;
            return $response;
        }

        $boxInfo = $this->repository->getBoxInfo($outboundOrderId,$boxBarcode);

        if(empty($boxInfo->outboundOrder) || empty($boxInfo->stockBox)) {

            $outboundOrderBoxInfo = $this->repository->getOutboundOrderInfoByBoxBarcode($boxBarcode);

            if(!empty($boxInfo->outboundOrder) && !empty($boxInfo->stockBox)) {
                $response->errorMessage = 'Этот короб из другого заказа:'. ArrayHelper::getValue($outboundOrderBoxInfo->outboundOrder,'order_number') .' / '.ArrayHelper::getValue($outboundOrderBoxInfo->outboundOrder,'parent_order_number');
            } else {
                $response->errorMessage = 'Этот короб не из нашей системы';
            }

            $response->isNotValid = true;
            return $response;
        }


        return $response;
    }
}