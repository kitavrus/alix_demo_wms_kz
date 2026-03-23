<?php
namespace common\ecommerce\defacto\api\service;

use Yii;
use common\ecommerce\entities\EcommerceApiInboundLog;
use common\ecommerce\entities\EcommerceApiOtherLog;
use common\ecommerce\entities\EcommerceApiOutboundLog;
use yii\helpers\ArrayHelper;

class EcommerceAPILogService
{
    private $inbound;
    private $other;
    private $outbound;

    //-----------------------------InBoundData------------------------------------------
    public function GetInBoundDataRequest($ourInboundId,$requestData) {
        return $this->inboundSaveRequest($ourInboundId,'GetInBoundData',serialize($requestData));
    }
    public function GetInBoundDataResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->inboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function GetLotContentRequest($ourInboundId,$requestData) {
        return $this->inboundSaveRequest($ourInboundId,'GetLotContent',serialize($requestData));
    }
    public function GetLotContentResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->inboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function SendInBoundFeedBackDataRequest($ourInboundId,$requestData) {
        return $this->inboundSaveRequest($ourInboundId,'SendInBoundFeedBackData',serialize($requestData));
    }
    public function SendInBoundFeedBackDataResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->inboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function ReturnShipmentRequest($ourInboundId,$requestData) {
        return $this->inboundSaveRequest($ourInboundId,'ReturnShipment',serialize($requestData));
    }
    public function ReturnShipmentResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->inboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }


    public function GetReturnReasonListRequest($requestData) {
        return $this->inboundSaveRequest(0,'GetReturnReasonList',serialize($requestData));
    }

    public function GetReturnReasonListResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->inboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function GetReturnReasonProcessListRequest($requestData) {
        return $this->inboundSaveRequest(0,'GetReturnReasonProcessList',serialize($requestData));
    }

    public function GetReturnReasonProcessListResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->inboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function GetShipmentForReturnRequest($requestData) {
        return $this->inboundSaveRequest(0,'GetShipmentForReturn',serialize($requestData));
    }

    public function GetShipmentForReturnResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->inboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }



    //-----------------------------Shipments------------------------------------------
    public function GetShipmentsRequest($requestData) {
       return $this->outboundSaveRequest('0','0','GetShipments',serialize($requestData));
    }
    public function GetShipmentsResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function CancellationRequestExistsByCustomerRequest($ourOutboundId,$requestData) {
        return $this->outboundSaveRequest($ourOutboundId,'0','CancellationRequestExistsByCustomer',serialize($requestData));
    }

    public function CancellationRequestExistsByCustomerResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function SendAcceptedShipmentsRequest($ourOutboundId,$requestData) {
        return $this->outboundSaveRequest($ourOutboundId,'0','SendAcceptedShipments',serialize($requestData));
    }

    public function SendAcceptedShipmentsResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function GetCargoLabelRequest($ourOutboundId,$requestData) {
        return $this->outboundSaveRequest($ourOutboundId,'0','GetCargoLabel',serialize($requestData));
    }

    public function GetCargoLabelResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }
	
	
	public function SendCargoDeliveryRequest($ourOutboundId,$requestData) {
		return $this->outboundSaveRequest($ourOutboundId,'0','SendCargoDelivery',serialize($requestData));
	}

	public function SendCargoDeliveryResponse($requestData) {
		$hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
		$errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
		return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
	}


    public function SendShipmentFeedbackRequest($ourOutboundId,$requestData) {
        return $this->outboundSaveRequest($ourOutboundId,'0','SendShipmentFeedback',serialize($requestData));
    }
    public function SendShipmentFeedbackResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }
    public function CancelShipmentRequest($ourOutboundId,$requestData) {
        return $this->outboundSaveRequest($ourOutboundId,'0','CancelShipment',serialize($requestData));
    }

    public function CancelShipmentResponse($requestData)
    {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }


    public function UploadShipmentFileRequest($ourOutboundId,$requestData) {
        return $this->outboundSaveRequest($ourOutboundId,'0','UploadShipmentFile',serialize($requestData));
    }

    public function UploadShipmentFileResponse($requestData)
    {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }
	
	
    public function GetBatchesRequest($requestData) {
        return $this->outboundSaveRequest('0','0','GetBatches',serialize($requestData));
    }

    public function GetBatchesResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function GetOutBoundRequest($requestData) {
        return $this->outboundSaveRequest('0','0','GetOutBound',serialize($requestData));
    }

    public function GetOutBoundResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function MarkBatchForCompletedRequest($requestData) {
        return $this->outboundSaveRequest('0','0','MarkBatchForCompleted',serialize($requestData));
    }

    public function MarkBatchForCompletedResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function SendOutBoundFeedBackRequest($requestData) {
        return $this->outboundSaveRequest('0','0','SendOutBoundFeedBack',serialize($requestData));
    }

    public function SendOutBoundFeedBackResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function CreateLcBarcodeRequest($requestData) {
        return $this->outboundSaveRequest('0','0','CreateLcBarcode',serialize($requestData));
    }

    public function CreateLcBarcodeResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->outboundSaveResponse($hasError,serialize($requestData),$errorMessage);
    }


    //-----------------------------OTHER------------------------------------------
    public function GetMasterDataRequest($requestData) {
        return $this->otherSaveRequest('GetMasterData',serialize($requestData));
    }

    public function GetMasterDataResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->otherSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function GetSkuInfoRequest($requestData) {
        return $this->otherSaveRequest('GetSkuInfo',serialize($requestData));
    }
    public function GetSkuInfoResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->otherSaveResponse($hasError,serialize($requestData),$errorMessage);
    }

    public function StockAdjustmentRequest($requestData) {
        return $this->otherSaveRequest('StockAdjustment',serialize($requestData));
    }

    public function StockAdjustmentResponse($requestData) {
        $hasError = intval(ArrayHelper::getValue($requestData,'HasError'));
        $errorMessage = ArrayHelper::getValue($requestData, 'ErrorMessage');
        return $this->otherSaveResponse($hasError,serialize($requestData),$errorMessage);
    }


    private function outboundSaveRequest($ourOutboundId,$ourOutboundItemId,$methodName,$requestData,$requestErrorMessage = '')
    {
        $request = new EcommerceApiOutboundLog();
        $request->our_outbound_id = $ourOutboundId;
        $request->our_outbound_item_id = $ourOutboundItemId;
        $request->method_name = $methodName;
        $request->request_data = $requestData;
        $request->request_error_message = $requestErrorMessage;
        $request->save(false);
        return $this->outbound = $request;
    }

    private function outboundSaveResponse($responseIsSuccess,$responseData,$responseErrorMessage = '')
    {
        $this->outbound->response_is_success = $responseIsSuccess;
        $this->outbound->response_data = $responseData;
        $this->outbound->response_error_message = $responseErrorMessage;
        $this->outbound->save(false);

        return $this->outbound;
    }

    private function inboundSaveRequest($ourInboundId,$methodName,$requestData,$requestErrorMessage = '')
    {
        $request = new EcommerceApiInboundLog();
        $request->our_inbound_id = $ourInboundId;
        $request->our_inbound_item_id = 0;
        $request->method_name = $methodName;
        $request->request_data = $requestData;
        $request->request_error_message = $requestErrorMessage;
        $request->save(false);
        return $this->inbound = $request;
    }

    private function inboundSaveResponse($responseIsSuccess,$responseData,$responseErrorMessage = '')
    {
        $this->inbound->response_is_success = $responseIsSuccess;
        $this->inbound->response_data = $responseData;
        $this->inbound->response_error_message = $responseErrorMessage;
        $this->inbound->save(false);

        return $this->inbound;
    }

    private function otherSaveRequest($methodName,$requestData,$requestErrorMessage = '')
    {
        $request = new EcommerceApiOtherLog();
        $request->method_name = $methodName;
        $request->request_data = $requestData;
        $request->request_error_message = $requestErrorMessage;
        $request->save(false);
        return $this->other = $request;
    }

    private function otherSaveResponse($responseIsSuccess,$responseData,$responseErrorMessage = '')
    {
        $this->other->response_is_success = $responseIsSuccess;
        $this->other->response_data = $responseData;
        $this->other->response_error_message = $responseErrorMessage;
        $this->other->save(false);

        return $this->other;
    }
}
