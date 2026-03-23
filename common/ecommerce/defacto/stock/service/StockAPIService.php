<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.01.2020
 * Time: 8:46
 */

namespace common\ecommerce\defacto\stock\service;


use common\ecommerce\defacto\api\service\EcommerceAPILogService;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;

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
        $this->api = new \common\ecommerce\defacto\api\ECommerceAPINew();
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
        $dirPath = 'api/de-facto/inventorySnapshot/'.date('Ymd').'/';
        BaseFileHelper::createDirectory($dirPath);
        $pathToFile = $dirPath.'SendInventorySnapshotRequest.txt';
        file_put_contents($pathToFile,print_r($request,true));
        $pathToFile = $dirPath.'SendInventorySnapshotRequestSerialize.txt';
        file_put_contents($pathToFile,serialize($request));
    }

    private function saveFileSendInventorySnapshotResponse($parsedResponse) {

        $dirPath = 'api/de-facto/inventorySnapshot/'.date('Ymd').'/';
        $pathToFile = $dirPath.'SendInventorySnapshotResponse.txt';
        file_put_contents($pathToFile,print_r($parsedResponse,true));
        $pathToFile = $dirPath.'SendInventorySnapshotResponseSerialize.txt';
        file_put_contents($pathToFile,serialize($parsedResponse));
    }



    private function makeSendInventorySnapshotRequest($aSnapshotRemainData)
    {
		// $snapshotDate = (new \DateTime())->format('Y-m-d H:i:s');
        $snapshotDate = (new \DateTime('now', new \DateTimeZone('Asia/Almaty')))->format('Y-m-d H:i:s P');
		
        $items = [];
        $items['B2CInventorySnapshotDto'] = [];
        foreach ($aSnapshotRemainData as $productInfo) {

            $placeAddressBarcode = !empty($productInfo['place_address_barcode']) ? $productInfo['place_address_barcode'] : '0-00-00-1';

            if(empty($productInfo['client_product_sku'])) {
                continue;
            }

            $items['B2CInventorySnapshotDto'][] = [
                'BusinessUnitId'=> $this->api->BUSINESS_UNIT_ID(),
                'SnapshotDate'=> $snapshotDate,
                'LocationId'=> $placeAddressBarcode, // Location of the product
                'PalletId'=>  $placeAddressBarcode, // Pallet number where the product is located
                'LcBarcode'=>  $productInfo['box_address_barcode'], // Pack barcode
                'SkuId'=>  $productInfo['client_product_sku'], // Product/Prepack SKU Id
                'SkuBarcode'=>  $productInfo['product_barcode'], // Product/Prepack Barcode
                'Quantity'=> $productInfo['productQty'],// Lot or product quantity
            ];
        }
		
		foreach($items['B2CInventorySnapshotDto'] as $row) {
            $rowToSave = $row['BusinessUnitId'].';';
            $rowToSave .= $row['SnapshotDate'].';';
            $rowToSave .= $row['LocationId'].';';
            $rowToSave .= $row['PalletId'].';';
            $rowToSave .= $row['LcBarcode'].';';
            $rowToSave .= $row['SkuId'].';';
            $rowToSave .= $row['SkuBarcode'].';';
            $rowToSave .= $row['Quantity'].';';
            file_put_contents('makeSendInventorySnapshotRequest-'.date('Y-m-d').'.csv',$rowToSave."\n",FILE_APPEND);
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
                $outResult['Data'] =  ArrayHelper::getValue($result, 'IsSuccess');

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
}
