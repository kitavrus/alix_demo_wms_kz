<?php

namespace app\modules\intermode\controllers\api\v1\outbound\service;

//use app\modules\ecommerce\controllers\intermode\inbound\domain\constants\InboundStatus;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\dto\add_order\AddOrderItemRequestDTO;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\dto\add_order\AddOrderRequestDTO;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInbound;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundDataMatrix;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundItem;
use app\modules\intermode\controllers\api\v1\inbound\constants\InboundStatus;
use app\modules\intermode\controllers\api\v1\outbound\dto\add_order\AddOrderItemRequestDTO;
use app\modules\intermode\controllers\api\v1\outbound\dto\add_order\AddOrderRequestDTO;
use app\modules\intermode\controllers\api\v1\outbound\repository\OutboundRepository;
use common\components\DeliveryProposalManager;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use app\modules\intermode\controllers\api\v1\outbound\dto\add_order\ValidateDTO;
use common\modules\outbound\models\OutboundOrder;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use yii\helpers\VarDumper;

class OutboundService
{
    private $repository;



    /**
     * InboundService constructor.
     */
    public function __construct()
    {
        $this->repository = new OutboundRepository();
    }

    public function getClientID()
	{
		return 103;
	}

	/**
	 * @param array $request
	 * @return ValidateDTO
	 */
	public function isNotValidAddOrderData($request) {
		$v = new ValidateDTO();
		if (!isset($request['order_id']) || !isset($request['items'])) {
			return$v ->withError("Не заполнен order_id или items");
		}

		if (empty(trim($request['order_id']))) {
			return $v->withError("Пустой order_id");
		}

		if (!isset($request['to_location'])) {
			return $v->withError("Не заполнен to_location");
		}
		$toLocation = intval(trim($request['to_location']));
		if (empty($toLocation)) {
			return $v->withError("Пустой to_location");
		}

		if (count($request['items']) < 1) {
			return $v->withError("Пустой items, нет товаров");
		}

		if ($this->repository->canChange($request['order_id'])) {
			return $v->withError("Такая накладная уже есть, она в работе");
		}

		return $v->withOutError("");
	}

	/**
	 * @param array $request
	 * @return AddOrderRequestDTO
	 */
	public function requestToCreateDTO($request) {
		$orderId = $request['order_id'];
		$toLocation = $request['to_location'];
		$comment = isset($request['comment']) ? $request['comment'] : "";
		$items = $request['items'];

		$dto = new AddOrderRequestDTO();
		$dto->clientId = $this->getClientID();
		$dto->orderNumber = $orderId;
		$dto->toLocation = $toLocation;
		$dto->comment = $comment;

		foreach ($items as $product) {
			$itemDto = new AddOrderItemRequestDTO();
			$itemDto->guid = $product["guid"];
			$itemDto->quantity = $product["quantity"];
			$dto->items[] = $itemDto;
			$dto->expectedQty += $itemDto->quantity;
		}

		return $dto;
	}

	/**
	 * @param AddOrderRequestDTO $createDTO
	 * @return OutboundOrder
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function addOrder($createDTO)
	{
//		VarDumper::dump($createDTO,10,true);
//		die;

		$order = $this->repository->getOrderByOrderNumber($createDTO->orderNumber);
		if (!empty($order)) {
			$order->delete();
			$this->repository->deleteOutboundOrderItem($order->id);
		}

		$order = $this->repository->createOrder($createDTO);
		$this->repository->createOrderItems($createDTO, $order->id);
		$this->createUpdateDeliveryProposalAndOrder($order);
		return $order;
	}

	/**
  	 * Create or update Delivery Proposal and Delivery Proposal Order
	 * @param OutboundOrder $outboundOrder
	 * @return TlDeliveryProposal
	 */
	public function createUpdateDeliveryProposalAndOrder($outboundOrder)
	{
			$update = false;
			if($dp = TlDeliveryProposal::find()
									   ->andWhere([
										   'route_from'=>$outboundOrder->from_point_id,
										   'route_to' =>$outboundOrder->to_point_id,
										   'client_id' => $this->getClientID(),
										   'status'=>[TlDeliveryProposal::STATUS_NEW]
									   ])->one()) {

				if (!($dpOrder = TlDeliveryProposalOrders::findOne(['client_id' =>  $this->getClientID(), 'order_id' => $outboundOrder->id, 'order_type'=>TlDeliveryProposalOrders::ORDER_TYPE_RPT, 'order_number' => $outboundOrder->order_number]))) {
					$dpOrder = new TlDeliveryProposalOrders();
				}
				$update = true;
			} else {
				$dp = new TlDeliveryProposal();
				if (!($dpOrder = TlDeliveryProposalOrders::findOne(['client_id' =>  $this->getClientID(), 'order_id' =>$outboundOrder->id, 'order_type'=>TlDeliveryProposalOrders::ORDER_TYPE_RPT, 'order_number' => $outboundOrder->order_number]))) {
					$dpOrder = new TlDeliveryProposalOrders();
				}
			}


			$dp->status = TlDeliveryProposal::STATUS_NEW;
			$dp->client_id =  $this->getClientID();
			$dp->route_from = $outboundOrder->from_point_id;
			$dp->route_to = $outboundOrder->to_point_id;
			$dp->cash_no = TlDeliveryProposal::METHOD_CHAR;
			$dp->save(false);

			if(!empty($deliveryProposalAttributes) && is_array($deliveryProposalAttributes)) {
				foreach($deliveryProposalAttributes as $field=>$value) {
					if($dp->hasAttribute($field)) {
						$dp->$field = $value;
						$dp->save(false);
					}
				}
			}

			if($dpOrder) {
				// Добавить заказы
				$dpOrder->client_id = $dp->client_id;
				$dpOrder->tl_delivery_proposal_id = $dp->id;
				$dpOrder->order_id = $outboundOrder->id;
				$dpOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
				$dpOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
				$dpOrder->order_number = $outboundOrder->parent_order_number . ' ' . $outboundOrder->order_number;
				$dpOrder->kg = $outboundOrder->kg;
				$dpOrder->kg_actual = $outboundOrder->kg;
				$dpOrder->mc = $outboundOrder->mc;
				$dpOrder->mc_actual = $outboundOrder->mc;
				$dpOrder->number_places = $outboundOrder->accepted_number_places_qty;
				$dpOrder->number_places_actual = $outboundOrder->accepted_number_places_qty;
				$dpOrder->title = $outboundOrder->title;
				$dpOrder->description = $outboundOrder->description;
				$dpOrder->save(false);
			}
			$dpManager = new DeliveryProposalManager(['id' => $dp->id]);
			if($update){
				$dpManager->onUpdateProposal();
			} else {
				$dpManager->onCreateProposal();
			}

			return $dp;
	}
	
	/**
	 * @param integer $outboundID
	 * @return OutboundOrder
	 */
	public function getOrderByID($outboundID)
	{
		return $this->repository->getOrderByID($outboundID);
	}
}