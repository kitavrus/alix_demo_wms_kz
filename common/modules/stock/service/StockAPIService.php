<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.01.2020
 * Time: 8:46
 */

namespace common\modules\stock\service;


use common\modules\client\models\Client;
use common\modules\stock\models\Stock;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;

class StockAPIService
{

    private $api;

    /*
 * @var array Default format function result
 * */
    private $_outResult = [
        'HasError' => true,
        'ErrorMessage' => '',
        'Message' => '',
        'Data' => [],
    ];

    /**
     * InboundAPI constructor.
     */
    public function __construct()
    {
        //$this->api = new \common\ecommerce\defacto\api\ECommerceAPI();
        $this->api = new DeFactoSoapAPIV2();
    }

    public function SendInventorySnapshot($aSnapshotRemainData)
    {
        $request = $this->makeSendInventorySnapshotRequest($aSnapshotRemainData);

        $this->saveFileSendInventorySnapshotRequest($request);
        $parsedResponse = [];
        $response = [];
        $response = $this->api->SendInventorySnapshot($request);
        $parsedResponse = $this->parseSendInventorySnapshot($response);
        $this->saveFileSendInventorySnapshotResponse($parsedResponse);

        return $parsedResponse;
    }

    private function saveFileSendInventorySnapshotRequest($request) {
        $dirPath = 'api-b2b/de-facto/inventorySnapshot/'.date('Ymd').'/';
        BaseFileHelper::createDirectory($dirPath);
        $pathToFile = $dirPath.'SendInventorySnapshotRequest.txt';
        file_put_contents($pathToFile,print_r($request,true));
        $pathToFile = $dirPath.'SendInventorySnapshotRequestSerialize.txt';
        file_put_contents($pathToFile,serialize($request));
    }

    private function saveFileSendInventorySnapshotResponse($parsedResponse) {

        $dirPath = 'api-b2b/de-facto/inventorySnapshot/'.date('Ymd').'/';
        $pathToFile = $dirPath.'SendInventorySnapshotResponse.txt';
        file_put_contents($pathToFile,print_r($parsedResponse,true));
        $pathToFile = $dirPath.'SendInventorySnapshotResponseSerialize.txt';
        file_put_contents($pathToFile,serialize($parsedResponse));
    }


    private function makeSendInventorySnapshotRequest($aSnapshotRemainData)
    {
        $snapshotDate = (new \DateTime('now', new \DateTimeZone('Asia/Almaty')))->format('Y-m-d H:i:s P');
        $items = [];
        $items['InventorySnapshotDto'] = [];
        foreach ($aSnapshotRemainData as $productInfo) {

            $placeAddressBarcode = !empty($productInfo['secondary_address']) ? $productInfo['secondary_address'] : '0-00-00-1';

            if(empty($productInfo['client_product_sku'])) {
                continue;
            }

            $items['InventorySnapshotDto'][] = [
                'BusinessUnitId'=> DeFactoSoapAPIV2::BUSINESS_UNIT_ID,
                'SnapshotDate'=> $snapshotDate,
                'LocationId'=> $placeAddressBarcode, // Location of the product
                'PalletId'=>  $placeAddressBarcode, // Pallet number where the product is located
                'LcBarcode'=>  $productInfo['primary_address'], // Pack barcode
                'SkuId'=>  $productInfo['client_product_sku'], // Product/Prepack SKU Id
                'SkuBarcode'=>  $productInfo['product_barcode'], // Product/Prepack Barcode
                'Quantity'=> $productInfo['productQty'],// Lot or product quantity
            ];
        }

        $request = [];
        $request['request']['InventorySnapshotList'] = $items;

        return $request;
    }

    private function parseSendInventorySnapshot($aApiResponse)
    {
        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'SendInventorySnapshotResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
                $outResult['Data'] =  @ArrayHelper::getValue($result, 'IsSuccess');

                return $outResult;
//                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.[' . ArrayHelper::getValue($result, 'Error') . ']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }


    /*
 * */
    public function helperUpdateDefactoSkuIdUpdate()
    {
        $lotsOnStock = Stock::find()
            ->select('product_barcode')
            ->andWhere([
                'client_id' => Client::CLIENT_DEFACTO,
                'status_availability' => Stock::STATUS_AVAILABILITY_YES,
            ])
            ->andWhere("field_extra1 = ''")
            ->groupBy('product_barcode')
            ->asArray()
            ->all();
        $x = [];
        foreach ($lotsOnStock as $lot) {

            $barcode = $lot['product_barcode'];

            if(!isset($x[$barcode])) {
                $skqId = $this->getAPISkuIdFromDefacto($lot['product_barcode']);

                $x[$barcode] = $skqId;
                Stock::updateAll(['field_extra1'=>$skqId],
                    [
                        'client_id'=>Client::CLIENT_DEFACTO,
                        'product_barcode'=>$lot['product_barcode'],
                    ]
                );
            }
        }
    }

    protected function getAPISkuIdFromDefacto($LotOrSingleBarcode)
    {
        if(!empty($LotOrSingleBarcode)) {

            $api = new DeFactoSoapAPIV2();
            $params['request'] = [
                'BusinessUnitId' => '1029',
                'PageSize' => 0,
                'PageIndex' => 0,
                'CountAllItems' => false,
                'ProcessRequestedDataType' => 'Full',
                'LotOrSingleBarcode' => $LotOrSingleBarcode,
            ];
//
            $result = $api->sendRequest('GetMasterData', $params);
            if ($resultDataArray = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.Data.MasterDataThreePL')) {
                $resultDataArray = count($resultDataArray) <= 1 ? [$resultDataArray] : $resultDataArray;
            } else {
                $resultDataArray = [];
            }

            foreach ($resultDataArray as $value) {
                return $value->SkuId;
            }
        }

        return '';
    }
}
