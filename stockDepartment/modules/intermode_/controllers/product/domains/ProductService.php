<?php

namespace stockDepartment\modules\intermode\controllers\product\domains;

use common\modules\product\models\ProductBarcodes;
use stockDepartment\modules\intermode\controllers\product\domains\dto\Filter;
use common\modules\product\models\Product;
use yii\helpers\VarDumper;

class ProductService
{
	private $repository;
	private $api;

	public function __construct()
	{
		$this->repository = new ProductRepository();
		$this->api = new ProductAPIService();
	}
	/**
	 * @param Filter|null $filter
	 * @return boolean
	 */
	public function getAndSaveMetaData($filter = null) {
		$data = $this->api->getMetaData($filter);
		$items = json_decode($data);
		foreach ($items as $item) {
			$productInfo = [
				'client_id' => $this->repository->getClientID(), // 103
				'guid' => trim($item->guid), // 91207409161 +
				'barcode' => $item->barcode, // 91207409161 +
				'model' => trim($item->article), // NKBQ4567001T75 +
				'color' => trim($item->color), // ЧЁРНЫЙ +
				'brand' =>trim($item->brand), // NIKE
				'category' => trim($item->category), // Кроссовки для города +
				'name' =>trim($item->name), // КРОССОВКИ ДЛЯ ГОРОДА NKBQ4567001T75
				'nameModel' =>trim($item->name_model), // NIKE AIR MAX 97
				'size' =>trim($item->size), // T75 +
				'gender' => trim($item->gender), // T75 +
			];

			//if (empty($filter) && $this->repository->isExistsGuid($productInfo['guid']) ) {
			//	continue;
		//	}
			$product = $this->repository->getORCreate($productInfo);
			foreach ($productInfo["barcode"] as $barcode) {
				$barcode = trim($barcode);
				//file_put_contents("getAndSaveMetaData.csv",$barcode."= 1",FILE_APPEND);
				if ($this->repository->isExistsBarcode($barcode)) {
					$barcode = ltrim($barcode,"0");
					//file_put_contents("getAndSaveMetaData.csv",$barcode."= 2",FILE_APPEND);
					if ($this->repository->isExistsBarcode($barcode)) {
						//file_put_contents("getAndSaveMetaData.csv",$barcode."= 3",FILE_APPEND);
						continue;
					}
				}
				//echo $barcode."\n";
				file_put_contents("getAndSaveMetaData.csv",$barcode."\n",FILE_APPEND);
				$productBarcode = new ProductBarcodes();
				$productBarcode->product_id = $product->id;
				$productBarcode->client_id = $this->repository->getClientID();
				$productBarcode->barcode = trim($barcode);
				$productBarcode->save(false);
			}
		}

		return true;
	}

	public function getByGuid($guid) {
		$p = new \stdClass();
		$p->product = $this->repository->getByGuid($guid);
		if (!empty($p->product)) {
			$p->barcodes = $this->repository->getBarcodesById($p->product->id);
		} else {
			$p->product = new Product();
			$p->barcodes = [];
		}
		return $p;
	}

	public function getProductIdByBarcode($barcode) {
		return $this->repository->getProductIdByBarcode($barcode);
	}

	public function getProductInfoByBarcode($barcode) {
		$product  = $this->repository->getProductByBarcode($barcode);

		$p = new \stdClass();
		$p->product = null;
		$p->barcodes = [];
		if(!empty($product)) {
			$p->product = $this->repository->getByGuid($product->client_product_id);
			$p->barcodes = $this->repository->getBarcodesById($p->product->id);
		}
		return $p;
	}

}