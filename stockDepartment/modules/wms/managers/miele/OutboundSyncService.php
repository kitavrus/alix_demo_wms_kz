<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 21.07.2017
 * Time: 13:09
 */

namespace stockDepartment\modules\wms\managers\miele;


use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\models\miele\repository\OutboundSyncRepository;

class OutboundSyncService
{
    private $repository;

    /**
     * OutboundSyncService constructor.
     */
    public function __construct()
    {
        $this->repository = new OutboundSyncRepository();
    }

    public function create($dto) {
        $this->repository->create($dto);
    }

    public function setOurStatusInWorking($orderClientId) {
        $this->repository->updateOurStatus($orderClientId,Stock::STATUS_OUTBOUND_SCANNING);
    }

    public function setOurStatusAccepted($orderClientId) {
        $this->repository->updateOurStatus($orderClientId,Stock::STATUS_OUTBOUND_FULL_RESERVED);
    }

    public function setOurStatusComplete($orderClientId) {
        $this->repository->updateOurStatus($orderClientId,Stock::STATUS_INBOUND_COMPLETE);
    }

    public function setClientStatus($orderClientId,$status) {
        return $this->repository->updateClientStatus($orderClientId,$status);
    }
}