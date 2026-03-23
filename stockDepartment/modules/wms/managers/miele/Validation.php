<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.07.2017
 * Time: 16:30
 */

namespace stockDepartment\modules\wms\managers\miele;


use common\modules\stock\models\ConstantZone;
use common\modules\stock\models\Stock;

class Validation
{
    private $repository;
    private $constants;

    public function __construct($repository)
    {
        $this->repository = $repository;
        $this->constants = new Constants();
    }
    // T
    public function canResendInboundOrder($id) {
        $inbound = $this->repository->inboundFindByID($id);
        if($inbound ) {
            if ($inbound->status != Stock::STATUS_INBOUND_NEW) {
                $extraField = unserialize($inbound->extra_fields);
                throw new \SoapFault("303", "Заявка " . $extraField->СтрочноеПредставлениеДокументаПрообраза . ' не может быть отправлена повторно, она в обработке на складе. Статус в системе ЛО : ' . "303");
            }
        }
    }
    // T
    public function canCancelInboundOrder($dto) {
        $inbound = $this->repository->inboundFindByID($dto['id']);
        if(!$inbound ) {
            throw new \SoapFault("301","Заявка c id ".$dto['id'].' не найдена. Статус в системе ЛО : '."301");
        }

        if( $inbound->status != Stock::STATUS_INBOUND_NEW) {
            $extraField = unserialize($inbound->extra_fields);
            throw new \SoapFault("302","Заявка ".$extraField->СтрочноеПредставлениеДокументаПрообраза.' не может быть отменена. Статус в системе ЛО : '."302");
        }
    }

    // T
    public function canResendOutboundOrder($id) {
        $outbound = $this->repository->outboundFindByID($id);
        if($outbound) {
            if ($outbound->status != Stock::STATUS_OUTBOUND_NEW) {
                $extraField = unserialize($outbound->extra_fields);
                throw new \SoapFault("304", "Заявка " . $extraField->СтрочноеПредставлениеДокументаПрообраза . ' не может быть отправлена повторно, она в обработке на складе. Статус в системе ЛО : ' . "304");
            }
        }
    }
    // T
    public function canCancelOutboundOrder($dto) {
        $outbound = $this->repository->outboundFindByID($dto['id']);
        if(!$outbound ) {
            throw new \SoapFault("305","Заявка c id ".$dto['id'].' не найдена. Статус в системе ЛО : '."305");
        }

        if( $outbound->status != Stock::STATUS_OUTBOUND_NEW) {
            $extraField = unserialize($outbound->extra_fields);
            $errorCode = $this->constants->getClientStatus(DTO::mapOutboundOurStatusToClient($outbound->status));

            throw new \SoapFault($errorCode,"Заявка ".$extraField->СтрочноеПредставлениеДокументаПрообраза.' не может быть отменена. Статус в системе ЛО : '.$errorCode);
        }
    }

    // T
    public function canResendMovementOrder($id) {
        $move = $this->repository->movementFindByID($id);
        if($move) {
            if ($move->status != ConstantZone::STATUS_NEW) {
                $extraField = unserialize($move->extra_fields);
                throw new \SoapFault("308", "Заявка " . $extraField->СтрочноеПредставлениеДокументаПрообраза . ' не может быть отправлена повторно, она в обработке на складе. Статус в системе ЛО : ' . "308");
            }
        }
    }
    // T
    public function canCancelMovementOrder($dto) {
        $move = $this->repository->movementFindByID($dto['id']);
        if(!$move ) {
            throw new \SoapFault("307","Заявка c id ".$dto['id'].' не найдена. Статус в системе ЛО : '."307");
        }

        if( $move->status != ConstantZone::STATUS_NEW) {
            $extraField = unserialize($move->extra_fields);
            $errorCode = $this->constants->getClientStatus(DTO::mapOutboundOurStatusToClient($move->status));

            throw new \SoapFault($errorCode,"Заявка ".$extraField->СтрочноеПредставлениеДокументаПрообраза.' не может быть отменена. Статус в системе ЛО : '.$errorCode);
        }
    }




    public function checkOutboundOrderIDs($ids) {
        return true;
    }

    public function checkOutboundOrder($dto) {
        return true;
    }

    public function checkInboundOrderIDs($dto) {

    }
}