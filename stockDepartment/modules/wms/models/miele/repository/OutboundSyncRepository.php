<?php
namespace stockDepartment\modules\wms\models\miele\repository;


use common\modules\client\models\Client;
use common\modules\inbound\models\OutboundOrderSyncValue;
use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\managers\miele\DTO;

class OutboundSyncRepository
{
    public function create($dto)
    {
        file_put_contents('OutboundSyncRepository-create.log', print_r($dto, true) . " / " . date('Ymd-H:i:s') . "\n", FILE_APPEND);

        $sync = OutboundOrderSyncValue::find()->andWhere([
            'client_id' => Client::CLIENT_MIELE,
            'outbound_client_id' => $dto->client_order_id,
        ])->one();

        if (!$sync) {
            $sync = new OutboundOrderSyncValue();
            $sync->client_id = Client::CLIENT_MIELE;
            $sync->outbound_client_id = $dto->client_order_id;//,//'8e45049f-6b8f-11e7-a28a-94de80bd5cf8';
        }

        $sync->outbound_id = $dto->outbound_id; //28425;
        $sync->status_our = $dto->status;
        $sync->status_client = 0;
        $sync->save(false);
    }

    public function updateOurStatus($outbound_client_id, $status)
    {
        $sync = OutboundOrderSyncValue::find()
            ->andWhere([
                'client_id' => Client::CLIENT_MIELE,
                'outbound_client_id' => $outbound_client_id,
            ])
            ->one();
        if ($sync) {
            file_put_contents('OutboundSyncRepository-updateOurStatus.log', $sync->status_our . ":" . $status . " / " . date('Ymd-H:i:s') . "\n", FILE_APPEND);
            $sync->status_our = $status;
            $sync->save(false);
        }
    }

    public function updateClientStatus($outbound_client_id, $status)
    {
        $sync = OutboundOrderSyncValue::find()
            ->andWhere([
                'client_id' => Client::CLIENT_MIELE,
                'outbound_client_id' => $outbound_client_id,
            ])
            ->one();
        if ($sync) {
            file_put_contents('OutboundSyncRepository-updateClientStatus.log', $sync->status_our . ":" . $status . " / " . date('Ymd-H:i:s') . "\n", FILE_APPEND);
            $sync->status_client = $status;
            $sync->save(false);
            return true;
        }
        return false;
    }
}