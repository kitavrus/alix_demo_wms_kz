<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 02.10.2017
 * Time: 20:26
 */

namespace common\modules\product\repository;

use common\modules\product\models\Product;
use common\modules\product\models\ProductBarcodes;
use common\overloads\ArrayHelper;

class ProductRepository
{
    /*
     * @return AR Product
     * */
    public function create($dto)
    {
        $product = new Product();
        $product->client_id = ArrayHelper::getValue($dto,'client_id',0);
//        $product->client_product_id = ArrayHelper::getValue($dto,'client_product_id',0);
        $product->name = ArrayHelper::getValue($dto,'name','');
        $product->sku = ArrayHelper::getValue($dto,'sku','');
        $product->model = ArrayHelper::getValue($dto,'model','');
        $product->status = ArrayHelper::getValue($dto,'status',Product::STATUS_ACTIVE);
		$product->name = ArrayHelper::getValue($dto,'name','');
		$product->color = ArrayHelper::getValue($dto,'color','');
		$product->size = ArrayHelper::getValue($dto,'size','');
		$product->category = ArrayHelper::getValue($dto,'category','');
		$product->gender = ArrayHelper::getValue($dto,'gender','');
		$product->field_extra1 = ArrayHelper::getValue($dto,'field_extra1',''); // Brand
		$product->field_extra2 = ArrayHelper::getValue($dto,'field_extra2','');

//        $product->price = ArrayHelper::getValue($dto,'price',0);
//        $product->weight_brutto = ArrayHelper::getValue($dto,'weight_brutto',0);
//        $product->weight_netto = ArrayHelper::getValue($dto,'weight_netto',0);
//        $product->m3 = ArrayHelper::getValue($dto,'m3',0);
//        $product->length = ArrayHelper::getValue($dto,'length',0);
//        $product->width = ArrayHelper::getValue($dto,'width',0);
//        $product->height = ArrayHelper::getValue($dto,'height',0);
//        $product->barcode = ArrayHelper::getValue($dto,'barcode','');
        $product->save(false);
        return $product;
    }

    /*
     * @param integer $$clientId
     * @param integer $barcode
     * @param integer $model or Article
     * @return boolean
     * */
    public function isExistsBarcode($clientId,$barcode)
    {
        return $this->isExists($clientId,$barcode);
    }
    /*
     * @param integer $clientId
     * @param integer $model or Article
     * @return boolean
     * */
    public function isExistsModel($clientId,$model)
    {
        return $this->isExists($clientId,'',$model);
    }

    /*
     * @param integer $clientId
     * @param integer $barcode
     * @param integer $model or Article
     * @return boolean
     * */
    public function isExistsBarcodeAndModel($clientId,$barcode,$model)
    {
        return $this->isExists($clientId,$barcode,$model);
    }

    public function isEmptyProductBarcodeByModel($clientId,$model) {
        return Product::find()->andWhere([
            'client_id'=>$clientId,
            'model'=>$model,
            'barcode'=>''
        ])->exists();
    }

    public function isProductDiffModel($clientId,$productBarcode,$productModel) {
        return Product::find()->andWhere([
            'client_id'=>$clientId,
            'model'=>$productModel,
            'barcode'=>$productBarcode
        ])->exists();
    }


    public function addBarcodeByModel($clientId,$barcode,$model) {
        return Product::updateAll(['barcode'=>$barcode],['client_id'=>$clientId,'model'=>$model]);
    }

    public function getProductBarcodeByModel($productModel,$clientId)
    {
        return Product::find()->select('barcode')->andWhere(['client_id'=>$clientId])
            ->andWhere(['model'=>$productModel])
            ->scalar();
    }

    public function getProductIDByModel($productModel,$clientId)
    {
        return Product::find()->select('id')->andWhere([
        	'client_id'=>$clientId,
			'model'=>$productModel,
		])->scalar();
    }

    public function getProductByModel($productModel,$clientId)
    {
        return Product::find()->select('id')->andWhere([
        	'client_id'=>$clientId,
			'model'=>$productModel,
		])->one();
    }

    public function getProductIDByBarcode($productBarcode,$clientId)
    {
        return ProductBarcodes::find()->select('product_id')->andWhere([
        	'client_id'=>$clientId,
			'barcode'=>$productBarcode,
		])->scalar();
    }

	public function getProductIDByModelOrCreate($dto)
	{
		$productID = Product::find()->select('id')->andWhere([
			'client_id'=>$dto->clientId,
			'model'=>$dto->model,
		])->scalar();

		if (empty($productID)){
			$createDTO = new \stdClass();
			$createDTO->client_id = $dto->clientId;
			$createDTO->model = $dto->model;
			$createDTO->name = $dto->name;
			$createDTO->color = $dto->color;
			$createDTO->field_extra1 = $dto->brand;
			$createDTO->category = $dto->category;
			$product = $this->create($createDTO);

			$productBarcode = new ProductBarcodes();
			$productBarcode->product_id = $product->id;
			$productBarcode->client_id = $dto->clientId;
			$productBarcode->barcode = $dto->barcode;
			$productBarcode->save(false);
			$productID = $product->id;
		}

		return $productID;
	}

//    public function createIfProductModelNoExist($clientId,$productModel,$dtoForCreateProduct)
//    {
//        if(!$this->isExistsModel($clientId,$productModel)) {
//            $this->create($dtoForCreateProduct);
//        }
//    }

    /*
     * @param integer $$clientId
     * @param integer $barcode
     * @param integer $model or Article
     * @return boolean
     * */
    private function isExists($clientId,$barcode = '',$model = '')
    {
        return Product::find()->andWhere(['client_id'=>$clientId])
            ->andFilterWhere(['barcode'=>$barcode])
            ->andFilterWhere(['model'=>$model])
            ->exists();
    }
	
	
	/*
	 * @param integer $$clientId
	 * @param integer $skuId
	 * @return array
	 * */
	public function getBarcodesBySkuId($clientId, $skuId)
	{
		return Product::find()->select('barcode')->andWhere(['client_id' => $clientId, 'client_product_id' => $skuId])->column();
	}

	/*
	 * @param integer $clientId
	 * @param integer $skuId
	 * @param integer $barcode
	 * @return boolean
	 * */
	public function isBarcodeExists($clientId, $skuId, $barcode) {
		return Product::find()->andWhere(['client_id'=>$clientId,'client_product_id'=>$skuId,'barcode'=>$barcode])->exists();
	}
	
}