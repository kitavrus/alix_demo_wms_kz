<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 22.09.2017
 * Time: 8:44
 */

namespace common\modules\transportLogistics\DeliveryProposalOrder\Validate;

use common\modules\transportLogistics\DeliveryProposalOrder\dto\Create;
use yii\db\Exception;

class Validate
{
    private $repository;

    /**
     * Create constructor.
     * @param $repository
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function canCreate(Create $data)
    {
        $can = $this->repository->exist([
            'client_id' => $data->clientId,
            'tl_delivery_proposal_id' => $data->deliveryProposalId,
            'order_type' => $data->orderType,
            'order_id' => $data->orderId,
        ]);
        if ($can) {
             throw new Exception("Заказ ".$data->orderNumber." уже существует");
        }
    }

}