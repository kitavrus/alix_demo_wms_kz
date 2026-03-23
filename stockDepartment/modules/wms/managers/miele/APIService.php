<?php
namespace stockDepartment\modules\wms\managers\miele;

use common\modules\movement\service\ServiceReservation;
use stockDepartment\modules\wms\managers\miele\Constants;
use stockDepartment\modules\wms\managers\miele\Repository;
use stockDepartment\modules\wms\managers\miele\DTO;
use stockDepartment\modules\wms\managers\miele\Validation;
use yii\helpers\VarDumper;

class APIService
{
    private $constant;
    private $repository;
    private $dto;
    private $validation;

    public function __construct()
    {
        $this->constant = new Constants();
        $this->repository = new Repository();
        $this->dto = new DTO();
        $this->validation = new Validation($this->repository);
    }

    // INBOUND
    //T
    public function SendInboundOrder($order)
    {
        $preparedOrder = $this->dto->instanceInbound()->prepareSendOrder($order->order); // ++
        $this->validation->canResendInboundOrder($preparedOrder['order']['client_order_id']); // ++
        $this->repository->createInbound($preparedOrder); // ++

        $inboundSync = new InboundSyncService();
        $inboundSync->create($this->repository->getDtoForSync());
        return $this->dto->instanceInbound()->makeResponseSendOrder(); // ++
    }
    //T
    public function GetInboundOrders($iDList)
    {
        $dto = $this->dto->instanceInbound()->prepareGetOrders($iDList); // ++
        $this->validation->checkInboundOrderIDs($dto); //TODO - ?
        $inbounds = $this->repository->getInbounds($dto); // + +
        return $this->dto->instanceInbound()->makeGetInboundOrders($inbounds); //++
    }
    //T
    public function GetChangedInboundOrders()
    {
        $orders = $this->repository->GetChangedInboundOrders();
//        return $this->dto->instanceInbound()->makeGetChangedOrders($orders);
        return $this->dto->instanceInbound()->makeObjectGetInboundOrders($orders);
    }
    //T
    public function MarkAsUnchangedInboundOrder($id, $status)
    {
        $dto = $this->dto->instanceInbound()->prepareMarkAsUnchangedOrder($id);
        $this->repository->markAsUnchangedInboundOrder($dto);
        return $this->dto->instanceInbound()->makeMarkAsUnchangedOrder();
    }
    //T
    public function CancelInboundOrder($request)
    {
        $id = $this->dto->instanceInbound()->prepareCancel($request); //++
        $this->validation->canCancelInboundOrder($id);// ++
        $this->repository->cancelInbounds($id); //++
        return $this->dto->instanceInbound()->makeResponseCancelOrder(); //++
    }

    // OUTBOUND
    // T
    public function SendOutboundOrder($order) {
        $preparedRequest = $this->dto->instanceOutbound()->prepareSendOrder($order);
        $this->validation->canResendOutboundOrder($order->order->Идентификатор);// ++
        $this->repository->createOutbound($preparedRequest);

        $outboundSync = new OutboundSyncService();
        $outboundSync->create($this->repository->getDtoForSyncOutbound());
        return $this->dto->instanceOutbound()->makeResponseSendOrder();
    }
    //T
    public function GetOutboundOrders($iDList)
    {
        $dtoRequest = $this->dto->instanceOutbound()->prepareRequestGetOrders($iDList);
        $this->validation->checkOutboundOrder($dtoRequest); // ?
        $outbounds = $this->repository->getOutbounds($dtoRequest);

        return $this->dto->InstanceOutbound()->makeResponseGetOrders($outbounds);
    }
    //T
    public function GetChangedOutboundOrders()
    {
        $changedOutbounds = $this->repository->getChangedOutbounds();
        return $this->dto->instanceOutbound()->makeResponseGetChangedOrders($changedOutbounds);
    }
    //T
    public function CancelOutboundOrder($request) {
        $dtoRequest = $this->dto->instanceOutbound()->prepareCancelOrder($request);
        $this->validation->canCancelOutboundOrder($dtoRequest);
        $this->repository->cancelOutbounds($dtoRequest);
        return $this->dto->InstanceOutbound()->makeResponseCancelOrder();
    }
    // T
    public function MarkAsUnchangedOutboundOrder($id, $status = '')
    {
        $dtoRequest = $this->dto->instanceOutbound()->prepareMarkAsUnchangedOrder($id);
        $this->repository->markAsUnchangedOutboundOrder($dtoRequest);
        return $this->dto->instanceOutbound()->makeResponseMarkAsUnchangedOrder();
    }

    // PRODUCTS
    // OK -Y
    public function UpdateMATMAS($list)
    {
        $dtoRequest = $this->dto->instanceMasterData()->parseRequestUpdateMATMAS($list);

//        file_put_contents('UpdateMATMAS-APIService.log',print_r($list,true)."\n"."\n",FILE_APPEND);
//        file_put_contents('UpdateMATMAS-APIService.log',print_r($dtoRequest,true)."\n"."\n",FILE_APPEND);

        $this->repository->updateMATMAS($dtoRequest);
        $response = $this->dto->instanceMasterData()->makeResponseUpdateMATMAS();
//        file_put_contents('UpdateMATMAS-APIService.log',print_r($response,true)."\n"."\n",FILE_APPEND);
        return $response;
    }

    // STOCK
    // TODO утачняем!!!
    public function GetStock($date)
    {
        $dtoRequest = $this->dto->instanceStock()->prepareGetStock($date);
        $stocks = $this->repository->getStock($dtoRequest);
        return $this->dto->instanceStock()->makeResponseGetStock($stocks);
    }
    // OK TODO утачняем!!! возвращает остатки только по фабричным номерам
    public function GetSerialStock($date = '', $materialNo = '',$articul = '')
    {
        $dtoRequest = $this->dto->instanceStock()->parseRequestGetSerial($date);
        $stocks =  $this->repository->getSerialStock($dtoRequest);
        return $this->dto->instanceStock()->makeResponseGetSerial($stocks);
    }

    // MOVEMENT
    // T
    public function SendMovementOrder($order)
    {
        $dtoRequest = $this->dto->instanceMovement()->prepareRequestSendOrder($order);
        $this->validation->canResendMovementOrder($dtoRequest['order']['client_order_id']);
        $this->repository->sendMovementOrder($dtoRequest);

        $movementSync = new MovementSyncService();
        $movementSync->create($this->repository->getDtoForSyncMovement());


        return $this->dto->instanceMovement()->makeResponseSendOrder();
    }

    // OK
    public function GetMovementOrders($id)
    {
        $dtoRequest = $this->dto->instanceMovement()->prepareRequestGetOrders($id);
        $orders = $this->repository->getMovementOrders($dtoRequest);
        return $this->dto->instanceMovement()->makeResponseGetOrders($orders);
    }

    public function GetChangedMovementOrders()
    {
        $orders = $this->repository->getChangedMovementOrders();
        $r = $this->dto->instanceMovement()->makeResponseGetChangedOrders($orders);
        file_put_contents('APIService-GetChangedMovementOrders.log',print_r($r,true),FILE_APPEND);
        return $r;
    }

    public function MarkAsUnchangedMovementOrder($id, $status = '') {
        $dtoRequest = $this->dto->instanceMovement()->parseMarkAsUnchangedOrder($id);
        $this->repository->markAsUnchangedMovementOrder($dtoRequest);
        return $this->dto->instanceMovement()->makeResponseMarkAsUnchangedOrder();
    }

    // OK
    public function CancelMovementOrder($id)
    {
        $dtoRequest = $this->dto->instanceMovement()->prepareRequestCancelOrder($id);
        $this->validation->canCancelMovementOrder($dtoRequest);
        $this->repository->cancelMovementOrder($dtoRequest);
        return $this->dto->instanceMovement()->makeResponseCancelOrder();
    }
    // T+
    public function GetProductMovement($beginDate = '',$endDate = '',$materialNo = '',$articul = '') {
        $dtoRequest = $this->dto->instanceMovement()->parseRequestGetProduct($beginDate);
        $products = $this->repository->getProductMovement($dtoRequest);
        return $this->dto->instanceMovement()->makeResponseGetProducts($products);
    }

    // STATUS AND ZONE
    public function getClientStatus()
    {

    }

    public function getClientZone()
    {
    }

    public function mapClientZoneToOurZone()
    {
    }

    public function mapOurZoneToClientZone()
    {
    }

    public function mapClientStatusToOurStatus()
    {
    }

    public function mapOurStatusToClientStatus()
    {
    }
}