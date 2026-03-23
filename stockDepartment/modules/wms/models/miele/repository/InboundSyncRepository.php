<?php
namespace stockDepartment\modules\wms\models\miele\repository;


use common\modules\client\models\Client;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\inbound\models\InboundOrderSyncValue;
use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\managers\miele\DTO;

class InboundSyncRepository
{
    public function create($dto) {

        file_put_contents('InboundSyncRepository-create.log',print_r($dto,true)." / ".date('Ymd-H:i:s')."\n",FILE_APPEND);

        $sync = InboundOrderSyncValue::find()->andWhere([
            'client_id'=> Client::CLIENT_MIELE,
            'inbound_client_id'=>$dto->client_order_id,
        ])->one();

        if(!$sync) {
            $sync = new InboundOrderSyncValue();
            $sync->client_id = Client::CLIENT_MIELE;
            $sync->inbound_client_id =  $dto->client_order_id;//,//'8e45049f-6b8f-11e7-a28a-94de80bd5cf8';
        }

        $sync->inbound_id = $dto->inbound_id; //28425;
        $sync->status_our = $dto->status;
        $sync->status_client = 0;
//        $sync->status_our = DTO::mapOurStatusToClient($dto->status);
//        $sync->status_client = DTO::mapOurStatusToClient(Stock::STATUS_INBOUND_NEW);
        $sync->save(false);
    }

    public function updateOurStatus($inbound_client_id,$status) {
        $sync = InboundOrderSyncValue::find()
                    ->andWhere([
                        'client_id'=>Client::CLIENT_MIELE,
                        'inbound_client_id'=>$inbound_client_id,
                    ])
                    ->one();
        if($sync) {
            file_put_contents('InboundSyncRepository-updateOurStatus.log',$sync->status_our.":".$status." / ".date('Ymd-H:i:s')."\n",FILE_APPEND);
            $sync->status_our =  $status;
            $sync->save(false);
            return true;
        }
        return false;
    }

    public function updateClientStatus($inbound_client_id,$status) {
        $sync = InboundOrderSyncValue::find()
            ->andWhere([
                'client_id'=>Client::CLIENT_MIELE,
                'inbound_client_id'=>$inbound_client_id,
            ])
            ->one();
        if($sync) {
            file_put_contents('InboundSyncRepository-updateClientStatus.log',$sync->status_our.":".$status." / ".date('Ymd-H:i:s')."\n",FILE_APPEND);
            $sync->status_client = $status;
//            $sync->status_client = DTO::mapOurStatusToClient($status);
            $sync->save(false);
            return true;
        }
        return false;
    }
}