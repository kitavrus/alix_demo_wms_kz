<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:13
 */

namespace stockDepartment\modules\wms\models\miele\repository;

use common\modules\movement\models\MovementOrderSyncValue;
use common\modules\client\models\Client;

class MovementSyncRepository
{
    public function create($dto)
    {
        file_put_contents('MovementRepository-create.log', print_r($dto, true) . " / " . date('Ymd-H:i:s') . "\n", FILE_APPEND);

        $sync = MovementOrderSyncValue::find()->andWhere([
            'client_id' => Client::CLIENT_MIELE,
            'movement_client_id' => $dto->client_order_id,
        ])->one();

        if (!$sync) {
            $sync = new MovementOrderSyncValue();
            $sync->client_id = Client::CLIENT_MIELE;
            $sync->movement_client_id = $dto->client_order_id;//,//'8e45049f-6b8f-11e7-a28a-94de80bd5cf8';
        }

        $sync->movement_id = $dto->movement_id; //28425;
        $sync->status_our = $dto->status;
        $sync->status_client = 0;
        $sync->save(false);
    }

    public function updateOurStatus($outbound_client_id, $status)
    {
        $sync = MovementOrderSyncValue::find()
            ->andWhere([
                'client_id' => Client::CLIENT_MIELE,
                'movement_client_id' => $outbound_client_id,
            ])
            ->one();
        if ($sync) {
            file_put_contents('MovementRepository-updateOurStatus.log', $sync->status_our . ":" . $status . " / " . date('Ymd-H:i:s') . "\n", FILE_APPEND);
            $sync->status_our = $status;
            $sync->save(false);
        }
    }

    public function updateClientStatus($outbound_client_id, $status)
    {
        $sync = MovementOrderSyncValue::find()
            ->andWhere([
                'client_id' => Client::CLIENT_MIELE,
                'movement_client_id' => $outbound_client_id,
            ])
            ->one();
        if ($sync) {
            file_put_contents('MovementRepository-updateClientStatus.log', $sync->status_our . ":" . $status . " / " . date('Ymd-H:i:s') . "\n", FILE_APPEND);
            $sync->status_client = $status;
            $sync->save(false);
            return true;
        }
        return false;
    }
}