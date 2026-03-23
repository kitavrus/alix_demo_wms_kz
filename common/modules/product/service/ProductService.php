<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 02.10.2017
 * Time: 20:26
 */

namespace common\modules\product\service;

use common\modules\client\models\Client;
use common\modules\product\models\Product;
use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;


class ProductService
{
    private $repository;
    private $dto;

    /**
     * ProductService constructor.
     * @param $dto array | \stdClass
     */
    public function __construct($dto = []) {
        $this->repository = new \common\modules\product\repository\ProductRepository();
        $this->dto = $dto;
    }

    public function create($dto)
    {
        return $this->repository->create($dto);
    }
    //
    public function createIfProductModelNoExist($clientId,$productModel,$dtoForCreateProduct)
    {
        if(!$this->repository->isExistsModel($clientId,$productModel)) {
            $this->repository->create($dtoForCreateProduct);
        }
    }
    //
    public function isExistsBarcode($clientId,$barcode)
    {
        return $this->repository->isExistsBarcode($clientId,$barcode);
    }
    //
    public function isExistsModel($clientId,$model)
    {
        return $this->repository->isExistsModel($clientId,$model);
    }
    //
    public function isExistsBarcodeAndModel($clientId,$barcode,$model)
    {
        return $this->repository->isExistsBarcodeAndModel($clientId,$barcode,$model);
    }
    //
    public function addBarcodeByModel($clientId,$barcode,$model)
    {
        if($this->isEmptyProductBarcodeByModel($clientId,$model)) {
            $this->repository->addBarcodeByModel($clientId,$barcode,$model);
        }
    }
    //
    public function isEmptyProductBarcodeByModel($clientId,$model) {
        return $this->repository->isEmptyProductBarcodeByModel($clientId,$model);
    }
    //
    public function isProductDiffModel($clientId,$productBarcode,$productModel)
    {
        return $this->repository->isProductDiffModel($clientId,$productBarcode,$productModel);
    }

    public function getProductBarcodeByModel($productModel,$clientId)
    {
        return $this->repository->getProductBarcodeByModel($productModel,$clientId);
    }

    public function getProductByModel($productModel,$clientId)
    {
        return $this->repository->getProductByModel($productModel,$clientId);
    }

    public function getProductIDByModel($productModel,$clientId)
    {
        return $this->repository->getProductIDByModel($productModel,$clientId);
    }

    public function getProductIDByBarcode($productBarcode,$clientId = 103)
    {
        return $this->repository->getProductIDByBarcode($productBarcode,$clientId);
    }

    public function getProductIDByModelOrCreate($dto)
    {
        return $this->repository->getProductIDByModelOrCreate($dto);
    }
	
	
	/*
	* update-product-barcodes-by-sku-id
	* @array $skuIds
	* */
	public function updateProductBarcodesBySkuId($skuIds)
	{
		$clientId = Client::CLIENT_DEFACTO;
		$lots = [];
		$api = new DeFactoSoapAPIV2();

		$skuIds = count($skuIds) == 1 ? [$skuIds] : $skuIds;
		foreach ($skuIds as $skuId) {

			$skuId = trim($skuId);
			if(empty($skuId)) {
				continue;
			}

			$dataFromAPI = $api->getMasterData($skuId);
			if ($dataFromAPI['HasError'] || empty($dataFromAPI['Data'])) {
				continue;
			}

			$resultDataArray = count($dataFromAPI['Data']) == 1 ? [$dataFromAPI['Data']] : $dataFromAPI['Data'];

			foreach ($resultDataArray as $resultData) {

				if($this->isBarcodeExists($clientId,$skuId,$resultData->LotOrSingleBarcode)) {
					continue;
				}

				$product = new Product();
				$product->client_id = $clientId;
				$product->client_product_id = $skuId;
				$product->barcode = $resultData->LotOrSingleBarcode;
				$product->created_user_id = 1;
				$product->updated_user_id = 1;
				$product->save(false);

				$lots [] =  $resultData->LotOrSingleBarcode;
			}
		}

		return $lots;
	}

	/*
	 * @param integer $$clientId
	 * @param integer $skuId
	 * @return array
	 * */
	public function getBarcodesBySkuId($clientId,$skuId)
	{
		return $this->repository->getBarcodesBySkuId($clientId,$skuId);
	}

	/*
	 * @param integer $clientId
	 * @param integer $skuId
	 * @param integer $barcode
	 * @return array
	 * */
	public function isBarcodeExists($clientId, $skuId, $barcode)
	{
		return $this->repository->isBarcodeExists($clientId, $skuId, $barcode);
	}
	
}