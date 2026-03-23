<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */
namespace common\ecommerce\defacto\outbound\service;

use common\ecommerce\constants\OutboundCancelStatus;
use common\ecommerce\constants\OutboundStatus;
use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;
use common\ecommerce\entities\EcommerceApiOutboundLog;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceStock;
use Imagine\Image\Box;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;
use yii\imagine\Image;
use Yii;

//use common\modules\outbound\models\OutboundPickingLists;
//use common\ecommerce\defacto\outbound\service\OutboundReservationService;

class OutboundService
{
    private $repository;
    private $api;
    private $dto;
    private $pathToSaveCargoLabel;
    private $pathToSaveWaybillDocument;

    public function __construct($dto = [])
    {
        $this->repository = new OutboundRepository();
        $this->api = new \common\ecommerce\defacto\outbound\service\OutboundAPIService();
        $this->dto = $dto;
        $this->pathToSaveCargoLabel = 'api/de-facto/cargo-label/'.date('Ymd');
        $this->pathToSaveWaybillDocument = 'api/de-facto/waybill/'.date('Ymd');
    }

    public function getOrdersForPrintPickingList()
    {
        return $this->repository->getOrdersForPrintPickList();
    }

    public function getOrderInfoByOrderNumber($orderNumber)
    {
        if($this->repository->isOrderExist($orderNumber)) {
            $order = $this->repository->getOrderByOrderNumber($orderNumber);
            return $this->getOrderInfo($order->id);
        }

        return false;
    }

    public function getOrderInfo($id = null)
    {
//        if(is_null($id)) {
//            $id = is_null($id) ? isset($this->dto->order->id) ? $this->dto->order->id : 0 : $id;
//        }
        return $this->repository->getOrderInfo($id);
//        return $this->repository->getOrderInfo($this->dto->order->id);
    }

    public function runReservation($outboundInfo) {
        if(isset($outboundInfo->order) && (int)$outboundInfo->order->allocated_qty < 1) {
            $outboundReservation = new \common\ecommerce\defacto\outbound\service\OutboundReservationService();
            $outboundReservation->run($outboundInfo);
        }
    }

//    public function qtyProductInBox()
//    {
//        return $this->repository->qtyProductInBox($this->dto->order->id, $this->dto->boxBarcode);
//    }

    public function makeScanned()
    {
        $this->repository->makeScannedProduct($this->dto);
    }

//    public function makeScannedFab()
//    {
//        $this->repository->makeScannedFab($this->dto);
//    }

    public function makePrintBoxLabel()
    {
        $this->repository->makePrintBoxLabel($this->dto);
    }

//    public function cleanBox()
//    {
//        $this->repository->cleanBox($this->dto);
//    }

    public function getOrderForComplete()
    {
        return $this->repository->getOrderForComplete();
    }

//    public function getOrderItemsForDiffReport()
//    {
//        return $this->repository->getOrderItemsForDiffReport($this->dto->order->id);
////        return $this->repository->getOrderItemsForDiffReport($this->dto->pickList->id);
//    }

//    public function getBoxesInOrder()
//    {
//        return $this->repository->getBoxesInOrder($this->dto->order->id);
//    }

    public function acceptedOrder($orderId) {
        $this->repository->acceptedOrder($orderId);
    }

//    public function create($dto) {
//        $dto->clientId = $this->repository->getClientID();
//        $this->outboundService->create($dto);
//    }
    public function getClientID()
    {
        return $this->repository->getClientID();
    }

//    public function getOrderItemsByPickListBarcode($pickListBarcode)
//    {
//        $pickListIDs = OutboundPickingLists::getPickingListIDsByPickingListBarcode($pickListBarcode);
//        return OutboundPickingLists::getStockByPickingIDs($pickListIDs);
//    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    public function showOrderItems($pickListBarcode)
    {
        $items =  $this->repository->showOrderItems($pickListBarcode);
        return $items;
    }

    public function packageBarcodeInfo($pickListBarcode,$packageBarcode)
    {
        $qtyProductInPackage = $this->repository->qtyProductInPackage($pickListBarcode,$packageBarcode);
        return [
            'qtyProductInPackage'=>$qtyProductInPackage,
        ];
    }

    public function usePackageBarcodeInOtherOrder($pickListBarcode,$packageBarcode)
    {
       return $this->repository->usePackageBarcodeInOtherOrder($pickListBarcode,$packageBarcode);
    }

    public function emptyPackage($dto)
    {
        $this->repository->emptyPackage($dto);
    }

    /*
    * Re Allocate outbound order
    * @param integer $outbound_id
    * */
    public static function resetByOutboundOrderId($outbound_order_id)
    {
        if ($outboundOrder = EcommerceOutbound::findOne($outbound_order_id)) {
            EcommerceOutbound::updateAll(['accepted_qty' => '0', 'allocated_qty' => '0', 'status' => OutboundStatus::_NEW], ['id' => $outboundOrder->id]);
            EcommerceOutboundItem::updateAll(['accepted_qty' => '0','allocated_qty' => '0', 'status' => OutboundStatus::_NEW], ['outbound_id' => $outboundOrder->id]);
            EcommerceStock::updateAll([
                'outbound_box' => '',
                'outbound_id' => 0,
                'outbound_item_id' => 0,
                'scan_out_datetime' => 0,
                'scan_out_employee_id' => 0,
                'status_outbound' => StockOutboundStatus::NOT_SET,
                'status_availability' => StockAvailability::YES
            ], ['outbound_id' => $outboundOrder->id]);
        }
    }

    public function GetShipments($OrderQuantity = 30) {
        $shipmentList = $this->api->GetShipments($OrderQuantity);
//        $shipmentList = EcommerceApiOutboundLog::find()->andWhere(['id'=>1])->one();
//        $shipmentList = unserialize($shipmentList->response_data);
//        VarDumper::dump(ArrayHelper::getValue($shipmentList,'Data'),10,true);
//        die;
       return $this->repository->create(ArrayHelper::getValue($shipmentList,'Data'));
    }

    public function SendShipmentFeedback($ourOutboundId)
    {
        $orderInfo = $this->getOrderInfo($ourOutboundId);

        if($orderInfo->order->expected_qty != $orderInfo->order->accepted_qty) {
            $orderInfo->order->client_CancelReason = \common\ecommerce\constants\OutboundCancelStatus::PARTIAL_CANCEL;
            $orderInfo->order->save(false);

            $orderInfo = $this->getOrderInfo($ourOutboundId);
            $this->api->PartialCancelShipment($orderInfo);
        }

        $response = $this->api->SendShipmentFeedback($orderInfo);

        if($response['HasError'] == false) {
            $this->setApiStatusYes($ourOutboundId);
        } else {
            $this->setApiStatusError($ourOutboundId);
        }

        return $response;
    }

    public function setApiStatusYes($ourOutboundId) {
        $this->repository->setApiStatus($ourOutboundId,StockAPIStatus::YES);
    }

    public function setApiStatusError($ourOutboundId) {
        $this->repository->setApiStatus($ourOutboundId,StockAPIStatus::ERROR);
    }

    public function CancellationRequestExistsByCustomer($ourOutboundId) {
        $orderInfo = $this->getOrderInfo($ourOutboundId);
        $apiResponse = $this->api->CancellationRequestExistsByCustomer($ourOutboundId,$orderInfo->order->order_number);

        if($apiResponse['HasError'] == false && $apiResponse['Data'] == true) {
            $this->repository->setStatusCancelByCustomer($ourOutboundId,OutboundCancelStatus::CUSTOMER_REQUESTS_CANCELLATION);
            //$this->CancelShipment($ourOutboundId,OutboundCancelStatus::CUSTOMER_REQUESTS_CANCELLATION);
        }

        return;
    }

    public function SendAcceptedShipments($ourOutboundId) {
        $orderInfo = $this->getOrderInfo($ourOutboundId);
        $apiResponse = $this->api->SendAcceptedShipments($ourOutboundId,$orderInfo->order->order_number);
        return $apiResponse;
    }

    public function reservationOrdersForPrintPickingList($outboundOrderIds) {

        $outboundList = [];
        foreach($outboundOrderIds as $id) {
            $service = new OutboundService();
            // Если ребята патаются напечатать листы сборки для собраных заказов
            if(!$service->canPrintPickingList($id)) {
                continue;
            }
            $service->SendAcceptedShipments($id);
            $service->CancellationRequestExistsByCustomer($id);
            // Если клинет отменил заказ, перепроверяем
            if(!$service->canPrintPickingList($id)) {
                continue;
            }

            $outboundList[] = $id;
        }

        $placeAddressSorting = new ReservationPlaceAddressSortingService();
        $beforeReservationSorting = $placeAddressSorting->beforeReservationSorting($outboundList);

        $service = new OutboundService();
        foreach($beforeReservationSorting as $id) {
            $service->runReservation($service->getOrderInfo($id));
        }

        return $placeAddressSorting->beforePrintPickingList($beforeReservationSorting);
    }

    public function CancelShipment($ourOutboundId,$reason)
    {
        $this->repository->setStatusCancelByAPI($ourOutboundId,$reason);

        $orderInfo = $this->getOrderInfo($ourOutboundId);

        $response = $this->api->CancelShipment($orderInfo);
        if($response['HasError'] == false) {
            $this->setApiStatusYes($ourOutboundId);
        } else {
            $this->setApiStatusError($ourOutboundId);
        }

        $this->resetByOutboundOrderId($ourOutboundId);
        $this->repository->setStatusCancelByAPI($ourOutboundId,$reason);
    }

    public function printBoxLabel($dto) {
			//die(" НЕ ПЕЧАТАЙ");
        $dto->order->kg = $dto->kg;
        $dto->order->package_type = $dto->packageType;
        $dto->order->save(false);

//        $this->SendAcceptedShipments($dto->order->id);
         //if($dto->order->id != 580) {
            $sendShipmentFeedbackResponse = $this->SendShipmentFeedback($dto->order->id);
        //}

        $cargoLabel = $this->GetCargoLabel($dto->order->id);

        if(empty($cargoLabel->FileData)) {
            return [
                'pathToCargoLabelFile'=>'',
                'pathToWaybillFile'=>'',
            ];
        }

        ///////////////////////////////////////////////////////////
//        BaseFileHelper::createDirectory($this->pathToSaveCargoLabel);
//        $fileName = $cargoLabel->ourOrderId.'.'.$cargoLabel->FileExtension;
//        $fullPathFileName = $this->pathToSaveCargoLabel.'/'.$fileName;
//        file_put_contents($fullPathFileName,base64_decode($cargoLabel->FileData));

        $pathToCargoLabelFile = $this->saveCargoLabel($cargoLabel);
        $pathToWaybillFile = $this->saveWaybillDocument($dto->order->id);

        $dto->order->kg = $dto->kg;
        $dto->order->path_to_cargo_label_file = $pathToCargoLabelFile;
        $dto->order->path_to_order_doc = $pathToWaybillFile;
        $dto->order->client_TrackingNumber = $cargoLabel->TrackingNumber;
        $dto->order->client_TrackingUrl = $cargoLabel->TrackingUrl;
        $dto->order->client_ReferenceNumber = $cargoLabel->ReferenceNumber;
        $dto->order->packing_date = time();
        $dto->order->status = OutboundStatus::PRINT_BOX_LABEL;
        $dto->order->save(false);
        ///////////////////////////////////////////////////////////
        $pathToCargoLabelFile = $dto->order->path_to_cargo_label_file;
        $pathToWaybillFile = $dto->order->path_to_order_doc;

        return [
            'pathToCargoLabelFile'=>$pathToCargoLabelFile,
            'pathToWaybillFile'=>$pathToWaybillFile,
        ];
    }

    public function GetCargoLabel($ourOutboundId) {
        $orderInfo = $this->getOrderInfo($ourOutboundId);
        $preparedResponseByAPI = $this->api->GetCargoLabel($orderInfo);
        $IsSuccess = ArrayHelper::getValue($preparedResponseByAPI,'Data.IsSuccess');
        $dataFromAPI = ArrayHelper::getValue($preparedResponseByAPI,'Data.Data');

        $result = new \stdClass();
        $result->FileExtension =  '';
        $result->FileData =  '';
        $result->ReferenceNumber =  '';
        $result->PageSize =  '';
        $result->TrackingNumber =  '';
        $result->TrackingUrl =  '';
        $result->ourOrderId = $ourOutboundId;

        if($IsSuccess) {
            $result->FileExtension =  ArrayHelper::getValue($dataFromAPI,'FileExtension');
            $result->FileData =  ArrayHelper::getValue($dataFromAPI,'FileData');
            $result->ReferenceNumber =  ArrayHelper::getValue($dataFromAPI,'ReferenceNumber');
            $result->PageSize =  ArrayHelper::getValue($dataFromAPI,'PageSize');
            $result->TrackingNumber =  @ArrayHelper::getValue($dataFromAPI,'TrackingNumber');
            $result->TrackingUrl =  @ArrayHelper::getValue($dataFromAPI,'TrackingUrl');
        }

        return $result;
    }

    public function getDataForSendByApi() {
        return $this->repository->getDataForSendByApi();
    }

    public function sendAllReadyToSendByApiOrders()
    {
        $resultList = [];
        $orderList = $this->getDataForSendByApi();

        if(empty($orderList)) {
            return $resultList;
        }

        foreach($orderList as $order) {
            $resultList[] = $this->SendShipmentFeedback($order->id);
        }

        return $resultList;
    }

    public function isPacked($ourOutboundId) {
        return $this->repository->isPacked($ourOutboundId);
    }

    public function getDataForPrintWaybill($ourOutboundId) {
        return $this->repository->getOrderInfo($ourOutboundId);
    }

    public function saveCargoLabel($cargoLabel) {

        BaseFileHelper::createDirectory($this->pathToSaveCargoLabel);
        $fileName = $cargoLabel->ourOrderId.'.'.$cargoLabel->FileExtension;
        $pathToDocument = $this->pathToSaveCargoLabel.'/'.$fileName;
        file_put_contents($pathToDocument,base64_decode($cargoLabel->FileData));

        return $pathToDocument;
    }


    public function StockAdjustment($LotOrSingleBarcode,$Quantity,$Operator) {
        return $this->api->StockAdjustment($LotOrSingleBarcode,$Quantity,$Operator);
    }

    public function UploadShipmentFile($ourOutboundId) {

        $orderInfo = $this->repository->getOrderInfo($ourOutboundId);

        $ExternalShipmentId = $orderInfo->order->order_number;
        $FileBase64 = $this->makeFileBase64($orderInfo->order->path_to_order_doc);

        return $this->api->UploadShipmentFile($ExternalShipmentId,$FileBase64);
    }

    private function makeFileBase64($pathToWaybillFile) {
        return base64_encode(file_get_contents($pathToWaybillFile));
    }



    public function saveWaybillDocument($outboundID) {
//        $pathToDocument = '';

        $orderInfo = $this->getDataForPrintWaybill($outboundID);

       // $this->pathToSaveWaybillDocument;

        $pdf = new \TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('arial', '', 8); //ok
//set margins
        $pdf->SetMargins(4, 4, 4,true);
// remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//set auto page breaks
//        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetAutoPageBreak(true, 5);
//set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
// consider changing to A5
        $pdf->AddPage('L', 'A4', true);
        $pdf->setJPEGQuality(100);

        $imgPath = \Yii::getAlias("@web/image/e-commerce/pdf/");

//$test = 0.0;
//$test = Yii::$app->formatter->asDecimal($test,2);

        $defactoTOO = "Defacto Retail Store Kz(Дефакто Ретэйл Стор Кз) Товарищество с ограниченной ответственностью";

        $clientName = $orderInfo->order->customer_name;// "Игорь Потема";
        $storeName = $orderInfo->order->client_StoreName;// "341 KZK ALMATY ADK MALL";
        $documentNumber = $orderInfo->order->order_number; // '3763669'; // Номер документа
        $orderNumber = substr($orderInfo->order->order_number,4,strlen($orderInfo->order->order_number)); //
        $customerAddress = $orderInfo->order->customer_address;
        $totalExpected = $orderInfo->order->accepted_qty;
        $totalPrice = $orderInfo->order->total_price;
        $totalPriceTax = $orderInfo->order->total_price_tax;
        $outboundBoxBarcode = $orderInfo->outboundBoxBarcode;

        $productsInOrder = $orderInfo->items;

        $dateTimeCreatedDocument = \Yii::$app->formatter->asDatetime(time(),'php:d.m.Y H:i:s'); // Дата составления

        $html ='<table width="100%" style="padding:2px" >
<tr>
    <td  width="80%">&nbsp;</td>
    <td  width="20%">Приложение 26<br /> к приказу Министра финансов<br /> Республики Казахстан<br /> от 20 декабря 2012 №562</td>
</tr>
</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $html ='<table width="100%" style="padding:2px" >
<tr>
    <td  width="30%">Организация(индивидуальный предприниматель)</td>
    <td  width="50%" style="border-bottom: 0.2px solid black; padding-top:10px; font-weight:bold;" align="center" >Defacto Retail Store Kz(Дефакто Ретэйл Стор Кз) Товарищество с<br />ограниченной ответственностью</td>
    <td  width="20%"><table width="100%" border="0"   style="padding-top:5px; padding-bottom:5px;"><tr><td width="30%" >ИИН/БИН</td><td width="70%" align="center" style="border: 0.2px solid black; font-weight:bold;">'.$documentNumber.'</td></tr></table> </td>
</tr>
</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        $html ='<table width="100%" style="padding:2px"><tr>
    <td  width="80%">&nbsp;</td>
    <td  width="20.4%">
        <table width="100%" border="1"  style="padding:5px">
            <tr><td width="40%" style="background-color:#c2ccd1;" align="center">Номер<br />документа</td><td  width="60%" style="background-color:#c2ccd1" align="center">Дата<br />составления</td></tr>
            <tr><td  align="center">'.$orderNumber.'</td><td>'.$dateTimeCreatedDocument.'</td></tr>
        </table>
    </td>
</tr>
</table>';

        $pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY()-5,$html,0,1,false,true,'R');
        $html ='<table width="100%" style="padding:2px" ><tr><td align="center"><h1>НАКЛАДНАЯ НА ОТПУСК ЗАПАСОВ НА</h1></td></tr></table>';
        $pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY()-1,$html,0,1,false,true,'C');

        $html ='<table width="100%" border="1"  style="padding:2px">
<tr>
    <td style="background-color:#c2ccd1;" align="center">Организация(индивидуальный<br/> предприниматель) - отправитель</td>
    <td style="background-color:#c2ccd1;" align="center">Организация(индивидуальный<br/> предприниматель) - получатель</td>
    <td style="background-color:#c2ccd1;" align="center">Ответственный за поставку<br/>(Ф.И.О)</td>
    <td style="background-color:#c2ccd1;" align="center">Транспортная накладная</td>
    <td style="background-color:#c2ccd1;" align="center">Товарно-транспортная накладная<br />(номер,дата)</td>
</tr>
<tr>
    <td>'.$defactoTOO.'</td>
    <td>'.$clientName.'</td>
    <td>'.$storeName.'<br />'.$outboundBoxBarcode.'</td>
    <td></td>
    <td></td>
</tr>
</table>';
        $pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY(),$html,0,1,false,true,'C');

        $html ='<table width="100%" border="1"  style="padding:2px">
            <tr>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Номер по<br/>подряду</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Наименование, характеристика</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Номенклатурный номер</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Единица<br />измерения</td>
                <td style="background-color:#c2ccd1;" align="center" colspan="2">Количество</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Цена за единицу,<br />в KZT</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Сумма НДС, в <br /> KZT</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Сумма НДС, в KZT</td>
            </tr>
            <tr>
                <td style="background-color:#c2ccd1;" align="center">подлежит отпуску</td>
                <td style="background-color:#c2ccd1;" align="center">отпущено</td>
            </tr>
       ';

        $rows  = '';
        $totalPrice = 0;
        $totalPriceTax = 0;
//        $totalPriceTax2 = 0;
        $totalPriceAndDiscount = 0;
        $rowIndex = 1;
        foreach($productsInOrder as $key=>$productRow)
        {
            if($productRow->accepted_qty < 1) { continue; }

//            $totalPrice += ($productRow->product_price * $productRow->accepted_qty);
//            $totalPriceTax += $productRow->price_tax;
//            $totalPriceTax2 += ($totalPrice - $productRow->price_discount);

            $totalPriceTax += $productRow->price_tax;
            $rowPrice = ($productRow->product_price * $productRow->accepted_qty);
            $rowPriceAndDiscount = ($rowPrice - $productRow->price_discount);

            $totalPrice += $rowPrice;
            $totalPriceAndDiscount += $rowPriceAndDiscount;

            $rows .= '
<tr>
    <td>'.($rowIndex++).'</td>
    <td>'.$productRow->product_model.'</td>
    <td>'.$productRow->product_name.'</td>
    <td>AD</td>
    <td>'.$productRow->accepted_qty.'</td>
    <td>'.$productRow->accepted_qty.'</td>
    <td>'.$productRow->product_price.'</td>
    <td>'.($rowPriceAndDiscount).'</td>
    <td>'.$productRow->price_tax.'</td>
</tr>
';
        }

        $tableEnd = ' </table>';
        $html .= $rows.$tableEnd;
        $pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY()+2,$html,0,1,false,true,'C');

        $html ='<table width="100%" border="1"  style="padding:3px">
<tr>
    <td colspan="4" align="right" style="font-weight:bold;">Итого</td>
    <td>'.$totalExpected.'</td>
    <td>'.$totalExpected.'</td>
    <td>'.$totalPrice.'</td>
    <td>'.$totalPriceAndDiscount.'</td>
    <td>'.$totalPriceTax.'</td>
</tr>
</table>
';

        $pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY()+5,$html,0,1,false,true,'C');
//$pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Ln();
        $html ='<table width="100%" border="0">
<tr>
    <td width="20%">Всего отпущено количество запасов</td>
    <td width="26%">____________________________________________</td>
    <td width="54%">на сумму (прописью), в KZT _______________________________________________________________________ </td>
</tr>
</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $html ='<table width="100%" border="0">
<tr>
<td>
<table width="100%" border="0">
        <tr>
            <td>
                <table width="100%" border="0">
                    <tr>
                        <td width="25%">Отпуск разрешил</td>
                        <td>____________________ /</td>
                        <td>____________________ /</td>
                        <td>_____________________</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="center" style="font-size: small;">должность</td>
                        <td align="center" style="font-size: small;" >подпись</td>
                        <td align="center" style="font-size: small;">расшифровка подписи</td>
                    </tr>
               </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0">
                    <tr>
                        <td width="25%">Главный бухгалтер</td>
                        <td width="25%">____________________ /</td>
                        <td width="50%">________________не предусмотрен____________</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">М.П.</td>
                        <td  align="center" style="font-size: small;">подпись</td>
                        <td  align="center" style="font-size: small;">расшифровка подписи</td>
                    </tr>
               </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                 <table width="100%" border="0">
                        <tr>
                            <td width="25%">Отпустил</td>
                            <td width="25%">____________________ /</td>
                            <td width="50%">__________________________________________</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td  align="center" style="font-size: small;">подпись</td>
                            <td  align="center" style="font-size: small;">расшифровка подписи</td>
                        </tr>
                 </table>
            </td>
        </tr>
</table>
</td>
<td>
 <table width="100%" border="0" style="padding: 2px">
        <tr>
            <td width="20%">По доверенности</td>
            <td width="40%">№_______________________________</td>
            <td width="40%">от "__"___________________20__года</td>
        </tr>
        <tr>
            <td  width="20%">выданной</td>
            <td colspan="2">____________________________________________________________________</td>
        </tr>
        <tr>
            <td colspan="3">_____________________________________________________________________________________</td>
        </tr>
        <tr>
            <td  width="20%">Запасы получил</td>
            <td>________________________________ /</td>
            <td>_________________________________</td>
        </tr>
        <tr>
            <td></td>
            <td align="center" style="font-size: small;">подпись</td>
            <td align="center" style="font-size: small;">расшифровка подписи</td>
        </tr>
 </table>
</td>
</tr>
</table>
';
        $pdf->writeHTML($html, true, false, true, false, '');
        // consider changing to A5
        $pdf->AddPage('L', 'A4', true);
        $pdf->setJPEGQuality(100);

        $table1 = '<table width="100%" border="0" style="padding:1px">
<tr>
<td style="background-color:#c2ccd1; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;" align="center" rowspan="2">Артикул</td>
<td style="background-color:#c2ccd1; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;" align="center" rowspan="2">Описание</td>
<td style="background-color:#c2ccd1; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;" align="center" rowspan="2">Количество</td>
<td style="background-color:#c2ccd1; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; " align="center" rowspan="2">Код причины возврата</td>
</tr>
<tr><td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black; "></td></tr>
<tr><td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black; "></td></tr>
<tr><td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black; "></td></tr>
<tr><td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black; "></td></tr>
<tr><td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black; "></td></tr>
<tr><td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td></tr>
<tr><td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td></tr>
<tr><td style="border-left: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black;"></td><td style="border-right: 1px solid black; border-bottom: 1px solid black;"></td><td style="border-right: 1px solid black; border-bottom: 1px solid black;"></td><td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td></tr>
</table>';

        $table2 = '<table width="100%" border="0"  style="padding:1px">
<tr>
<th style="background-color:#c2ccd1; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;" align="center"  colspan="2">Причина возврата</th>
</tr>
<tr>
<td style="background-color:#c2ccd1; border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;" align="center" width="25%">КОД</td>
<td style="background-color:#c2ccd1; border-bottom: 1px solid black; border-right: 1px solid black;" align="center" width="75%"></td>
</tr>
<tr>
<td  style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;" align="center" >1</td>
<td style="border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;">&nbsp;Бракованный товар</td>
</tr>
<tr>
<td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"  align="center" >2</td>
<td style="border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;">&nbsp;Неверный заказ</td>
</tr>
<tr>
<td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"  align="center" >3</td>
<td style="border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;">&nbsp;Посадка/Размер</td>
</tr>
<tr>
<td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"  align="center" >4</td>
<td style="border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;">&nbsp;Не нравится товар/ качество</td>
</tr>
<tr>
<td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"  align="center" >5</td>
<td style="border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;">&nbsp;Поздняя доставка</td>
</tr>
<tr>
<td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"  align="center" >6</td>
<td style="border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;">&nbsp;Поврежденная упаковка</td>
</tr>
<tr>
<td style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;"  align="center" >7</td>
<td style="border-bottom: 1px solid black; border-right: 1px solid black;">&nbsp;Другое</td>
</tr>
</table>
';

        $table3 = '<table width="100%" border="0"  style="padding:1px">
<tr>
<td width="60%">Покупатель Имя/Фамилия</td>
<td width="30%">Телефон:</td>
<td width="10%">Подпись:</td>
</tr>
</table>';

        $html ='<table width="100%" border="0" cellpadding="0" cellspacing="0"   style="padding:5px">
            <tr>
                <td width="60%"  align="right" style="border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"><h1>ФОРМА ВОЗВРАТА</h1></td>
                <td width="3%" style=" border-top: 1px solid black;" >&nbsp;</td>
                <td width="37%" style="border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
            </tr>
            <tr>
                <td width="60%" style="padding:0px">'.$table1.'</td>
                <td width="3%">&nbsp;</td>
                <td width="37%" style="padding:0px">'.$table2.'</td>
            </tr>
            <tr>
                <td  style="border-bottom: 1px solid black; border-right: 1px solid black;border-left: 1px solid black;"  colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="border-right: 1px solid black; border-left: 1px solid black;" colspan="3">'.$table3.'</td>
            </tr>
            <tr>
                <td style="border-right: 1px solid black; border-left: 1px solid black;" colspan="3"> Адрес:</td>
            </tr>
            <tr>
                <td style="border-top: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:bold;" colspan="3"> Пожалуйста отправьте копию вместе с заказом возврата</td>
            </tr>
        </table>';

//$pdf->StartTransform();
//$pdf->SetXY(0,0);
//$pdf->Rotate(-90,55,55);
//$pdf->writeHTMLCell(290,0,1,1,$html,0);
//$pdf->writeHTMLCell(290,0,1,1,$html,0);
        $pdf->writeHTML($html, true, false, true, false, '');
//$pdf->StopTransform();

        $pdf->StartTransform();
        $pdf->SetXY(0,0);
        $pdf->Rotate(-90,115,175);
        $html ='<table width="45%" border="0"  style="padding:9px">
<tr>
    <td align="center"><h1>Условия возврата</h1></td>
</tr>
<tr>
    <td style="font-size: 13px;">Вы можете вернуть товар в течении 30 дней с даты получения.<br />
        Пожалуйста, заполните нижеприведенную форму и отправте<br />
        посылку через службу доставки "Пони Экспресс".<br />
    </td>
</tr>
<tr><td style="font-weight:bold">Адрес возврата:</td></tr>
<tr>
    <td>040916, Казахстан, г. Алматы, Карасайский район<br />
       Ташкентский тракт, 17к,<br />
       Торговый центр "Aport", магазин "DeFacto".<br />
    </td>
</tr>
<tr>
    <td style="font-size: 13px;">Возврат средств будет произведен на банковскую карту в течение<br />
       10 рабочих дней, с момента получения товара на нашем складе.<br />
    </td>
</tr>
<tr>
    <td style="font-size: 13px;">Обращаем Ваше внимание на то, что возврат товара производится,<br />
       если указанный товар не был в употреблении, и в упакован оригинальную<br />
       упаковку, сохранены его товарный вид, потребительские свойства.<br />
       Продавец не несет ответственности, за неправильное использование товара,<br />
       преобритенного в интернет-магазине.<br />
    </td>
</tr>
<tr>
    <td style="font-size: 13px;">Нижнее белье, купальники и акссесуары не подлежат возврату.</td>
</tr>
<tr>
    <td style="font-weight:bold; font-size: 13px;">Вы можете связатся с нами по любым вопросам,<br />
        касающимся вашего заказа, отправив электронное<br />
        письмо на support.kz@defactofashion.com или <br />
        обратиться в нашу  службу поддержки клиентов 77172696766.
    </td>
</tr>
</table>';
//$pdf->SetFont('arial', '', 8); //ok
        $pdf->writeHTMLCell('','',5,0,$html);
        $pdf->StopTransform();

        $pdf->StartTransform();
        $pdf->SetXY(0,0);
        $pdf->Rotate(-90,35,95);
        $html ='<table width="45%" border="0"  style="padding:9px">
<tr>
    <td><img src="'.$imgPath.'/02.png" width="25" height="25" />&nbsp;&nbsp; Обмен в магазине</td>
</tr>
<tr>
    <td><img src="'.$imgPath.'/03.png" width="25" height="25" />&nbsp;&nbsp; Безопасная покупка</td>
</tr>
<tr>
    <td><img src="'.$imgPath.'/01.png" width="25" height="25" />&nbsp;&nbsp; Бесплатный возврат</td>
</tr>
<tr>
    <td><img src="'.$imgPath.'/DeFactoLogo.png"  /></td>
</tr>
<tr>
    <td style="font-size: 13px;">Для получения более подробной информации:</td>
</tr>
<tr>
    <td><img src="'.$imgPath.'/webAddress.png" width="25" height="25" />&nbsp;&nbsp; www.defactofashion.kz</td>
</tr>
<tr>
    <td><img src="'.$imgPath.'/email.png" width="25" height="25" />&nbsp;&nbsp; support.kz@defactofashion.com</td>
</tr>
<tr>
    <td><img src="'.$imgPath.'/phone.png" width="25" height="25" />&nbsp;&nbsp; +7 717 269 67 66</td>
</tr>
</table>';
//$pdf->SetFont('arial', '', 8); //ok
        $pdf->writeHTMLCell('','',5,0,$html);
        $pdf->StopTransform();
        $pdf->lastPage();

//        $pathToSave = "api/de-facto/waybill/".date('Ymd');
        $pathToDocument = $this->pathToSaveWaybillDocument;
        \yii\helpers\BaseFileHelper::createDirectory($pathToDocument);

        $pathToDocument = $pathToDocument.'/' . $orderNumber . '-waybill.pdf';
        $pdf->Output($pathToDocument, 'F');
//        $pdf->Output(time() . '-waybill.pdf', 'D');

        return $pathToDocument;
    }

    public function canPrintPickingList($outboundId) {
        return $this->repository->canPrintPickingList($outboundId);
    }

    public function isOrderExist($orderNumber)
    {
       return $this->repository->isOrderExist($orderNumber);
    }
}
