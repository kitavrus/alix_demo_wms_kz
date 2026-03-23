<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 21.07.2017
 * Time: 13:09
 */

namespace stockDepartment\modules\wms\managers\miele;


use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\models\miele\repository\MovementSyncRepository;
use stockDepartment\modules\wms\models\miele\repository\OutboundSyncRepository;

class MovementSyncService
{
    private $repository;

    /**
     * OutboundSyncService constructor.
     */
    public function __construct()
    {
        $this->repository = new MovementSyncRepository();
    }

    public function create($dto) {
        $this->repository->create($dto);
    }

    public function setOurStatusInWorking($orderClientId) {
        $this->repository->updateOurStatus($orderClientId,Constants::STATUS_IN_WORKING);
    }

    public function setOurStatusAccepted($orderClientId) {
        $this->repository->updateOurStatus($orderClientId,Constants::STATUS_RESERVED);
    }

    public function setOurStatusComplete($orderClientId) {
        $this->repository->updateOurStatus($orderClientId,Constants::STATUS_COMPLETE);
    }

    public function setClientStatus($orderClientId,$status) {
        return $this->repository->updateClientStatus($orderClientId,$status);
    }
}