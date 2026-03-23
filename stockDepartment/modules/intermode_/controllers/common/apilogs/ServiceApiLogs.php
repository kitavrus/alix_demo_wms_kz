<?php

namespace app\modules\intermode\controllers\common\apilogs;


use app\modules\intermode\controllers\common\apilogs\consts\OrderType;
use app\modules\intermode\controllers\common\apilogs\dto\AddRequest;
use app\modules\intermode\controllers\common\apilogs\dto\AddResponse;
use app\modules\intermode\controllers\common\apilogs\models\ApiLogs;
use yii\helpers\VarDumper;

class ServiceApiLogs
{
	private $current_id = 0;
	/**
	 * @param $add AddRequest
	 * @return integer
	 * */
	private function addRequest($add)
	{
		 $e = new ApiLogs();
		 $e->our_order_id = $add->our_order_id;
		 $e->their_order_number = $add->their_order_number;
		 $e->method_name = $add->method_name;
		 $e->order_type = $add->order_type;
		 $e->request_data = $add->request_data;
		 $e->request_status = $add->request_status;
		 $e->save(false);
		 return $this->current_id =  $e->id;
	}

	public function getCurrentID() {
		return $this->current_id;
	}

	/**
	 * @param $add AddResponse
	 * @return void
	 * */
	public function addResponse($add)
	{
		$parsedData = $this->DecodeResponseData($add->response_data);
		$responseMessage = "";
		if (!empty($parsedData)) {
			$responseMessage = isset($parsedData["message"]) ? $parsedData["message"] : $add->response_message;
		}

		$e = ApiLogs::findOne($add->id);
		$e->response_code = $add->response_code;
		$e->response_data = $add->response_data;
		$e->response_message = $responseMessage;
		$e->save(false);
	}

	public function addB2BInboundRequest($our_order_id,$their_order_number,$status,$request_data) {
		$r = new AddRequest();
		$r->our_order_id = $our_order_id;
		$r->their_order_number = $their_order_number;
		$r->request_data = $this->ConvertRequestData($request_data);
		$r->request_status = $status;
		$r->method_name = "";
		$r->order_type = OrderType::B2B_INBOUND;

		return $this->addRequest($r);
	}
	public function addB2BOutboundRequest($our_order_id,$their_order_number,$status,$request_data) {
		$r = new AddRequest();
		$r->our_order_id = $our_order_id;
		$r->their_order_number = $their_order_number;
		$r->request_data = $this->ConvertRequestData($request_data);
		$r->request_status = $status;
		$r->method_name = "";
		$r->order_type = OrderType::B2B_OUTBOUND;
		return $this->addRequest($r);
	}
	public function addB2BReturnRequest($our_order_id,$their_order_number,$status,$request_data) {
		$r = new AddRequest();
		$r->our_order_id = $our_order_id;
		$r->their_order_number = $their_order_number;
		$r->request_data = $this->ConvertRequestData($request_data);
		$r->request_status = $status;
		$r->method_name = "";
		$r->order_type = OrderType::B2B_RETURN;
		return $this->addRequest($r);
	}

	public function addB2COutboundRequest($our_order_id,$their_order_number,$status,$request_data) {
		$r = new AddRequest();
		$r->our_order_id = $our_order_id;
		$r->their_order_number = $their_order_number;
		$r->request_data = $this->ConvertRequestData($request_data);
		$r->request_status = $status;
		$r->method_name = "";
		$r->order_type = OrderType::B2C_OUTBOUND;
		return $this->addRequest($r);
	}

	private function ConvertRequestData($data) {
		return json_encode($data);
	}
	private function DecodeResponseData($data) {
		return json_decode($data,true);
	}
}