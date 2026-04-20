<?php

namespace stockDepartment\modules\alix\controllers\product\domains;

use common\modules\product\models\Product;
use common\modules\product\models\ProductBarcodes;
use common\overloads\ArrayHelper;
use stockDepartment\modules\kaspi\models\ProductBarcodesV2;
use stockDepartment\modules\kaspi\models\ProductV2;

class ProductRepository
{
	/**
	 */
	public function getClientID()
	{
		return 103;
	}

	/*
	 * @return AR Product
	 * */
	public function getORCreate($dto)
	{
		$product = new Product();
		if ($p = $this->getByGuid(ArrayHelper::getValue($dto,'guid',0))) {
			return $p;
		}
		$product->client_id = ArrayHelper::getValue($dto,'client_id',0);
        $product->client_product_id = ArrayHelper::getValue($dto,'guid',0);
		$product->name = ArrayHelper::getValue($dto,'name','');
		$product->sku = ArrayHelper::getValue($dto,'sku','');
		$product->model = ArrayHelper::getValue($dto,'model','');
		$product->status = ArrayHelper::getValue($dto,'status',Product::STATUS_ACTIVE);
		$product->name = ArrayHelper::getValue($dto,'name','');
		$product->color = ArrayHelper::getValue($dto,'color','');
		$product->size = ArrayHelper::getValue($dto,'size','');
		$product->category = ArrayHelper::getValue($dto,'category','');
		$product->gender = ArrayHelper::getValue($dto,'gender','');
		$product->field_extra1 = ArrayHelper::getValue($dto,'brand',''); // Brand
		$product->field_extra2 = ArrayHelper::getValue($dto,'nameModel','');
		$product->save(false);
		return $product;
	}

	/**
	* @param string $barcode
	* @return array|bool|\yii\db\ActiveRecord
	* */
	public function isExistsBarcode($barcode)
	{
		return ProductBarcodes::find()->select("id")->andWhere([
			'client_id'=>$this->getClientID(),
			'barcode'=>$barcode
		])->one();
	}

	/**
	 * @param string $barcode
	 * @return array|bool|\yii\db\ActiveRecord
	 * */
	public function isExistsBarcodeWithGuid($barcode,$productId)
	{
		return ProductBarcodes::find()
							  ->select("id")
							  ->andWhere([
								'barcode'=>$barcode,
								'product_id'=>$productId
							])->one();
	}

	/**
	 * @param string $article
	 * @return array|bool|\yii\db\ActiveRecord
	 * */
	public function isExistsModel($article)
	{
		return Product::find()->select("id")->andWhere([
			'client_id'=>$this->getClientID(),
			'model'=>$article
		])->one();
	}
	/**
	 * @param string $guid
	 * @return array|bool|\yii\db\ActiveRecord
	 * */
	public function isExistsGuid($guid)
	{
		return Product::find()->select("id")->andWhere([
			'client_id'=>$this->getClientID(),
			'client_product_id'=>$guid
		])->one();
	}

	/**
	 * @param string $guid
	 * @return array|bool|\yii\db\ActiveRecord
	 * */
/*	public function deleteAllByGuid($guid)
	{
		$pId = Product::find()->select("id")->andWhere([
			'client_id'=>$this->getClientID(),
			'client_product_id'=>$guid
		])->one();

		//Product::deleteAll(["id"=>$pId]);
		ProductBarcodes::deleteAll(["product_id"=>$pId]);
	}*/

	/**
	 * @param string $guid
	 * @return array|bool|\yii\db\ActiveRecord
	 * */
	public function getByGuid($guid)
	{
		return ProductV2::findByGuid($guid);
	}

	/**
	 * @param string $article
	 * @return ProductV2|null
	 */
	public function getByArticle($article)
	{
		if ($article === null || $article === '') {
			return null;
		}
		return ProductV2::find()->andWhere(['article' => $article])->one();
	}

	/**
	 * Ищет ProductV2 через product_barcodes_v2.
	 *
	 * @param string $barcode
	 * @return ProductV2|null
	 */
	public function getByBarcodeV2($barcode)
	{
		if ($barcode === null || $barcode === '') {
			return null;
		}
		$row = ProductBarcodesV2::find()->andWhere(['barcode' => $barcode])->one();
		if (!$row) {
			return null;
		}
		return ProductV2::findOne($row->product_id);
	}
	/**
	 * @param string $id
	 * @return string[]
	 * */
	public function getBarcodesById($id)
	{
		return
			\yii\helpers\ArrayHelper::map(
			ProductBarcodesV2::find()->andWhere([
			'product_id'=>$id
		])->asArray()
		  ->all()
		,"id","barcode");
	}

	/**
	 * @param string $barcode
	 * @return string
	 * */
	public function getProductIdByBarcode($barcode)
	{
		return ProductBarcodes::find()->select("product_id")->andWhere([
			'client_id'=>$this->getClientID(),
			'barcode'=>$barcode
		])->orderBy("id DESC")->scalar();
	}
	
	/**
	 * @param string $barcode
	 * @return Product
	 * */
	public function getProductByBarcode($barcode)
	{
		return Product::findOne(ProductBarcodes::find()
											   ->select("product_id")
											   ->andWhere([
													'client_id'=>$this->getClientID(),
													'barcode'=>$barcode
												])->orderBy("id DESC")
		);
	}

	/**
	 * @param string $barcode
	 * @return Product
	 * */
	public function getLastProductByBarcode($barcode)
	{
		return Product::find()->andWhere(["id"=>ProductBarcodes::find()
															   ->select("product_id")
															   ->andWhere([
																   'client_id'=>$this->getClientID(),
																   'barcode'=>$barcode
															   ])->orderBy("id DESC")]
		)->orderBy("id DESC")->one();
	}
	
	/**
	 * @param integer $productId
	 * @param string $barcode
	 * @return array|bool|\yii\db\ActiveRecord
	 * */
	public function isExistsBarcodeInOtherProduct($productId,$barcode,$barcodeWithOutFirst0)
	{
		return ProductBarcodes::find()->select("id")
									  ->andWhere([
									  	"product_id != :product_id and (barcode != :barcode OR barcode != :barcodeWithOutFirst0)"
									  ],[
									  	":product_id"=>$productId,
										  ":barcode"=>$barcode,
										  ":barcodeWithOutFirst0"=>$barcodeWithOutFirst0,
									  ])
		  ->one();
	}
}