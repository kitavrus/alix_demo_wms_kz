<?php
namespace stockDepartment\modules\mobile\controllers;

class ApiController extends \yii\web\Controller
{
    public function actionGetById($id)
    {
        return $this->asJson($this->products($id));
    }

    public function actionGetProducts() {
        return $this->asJson($this->products());
	}

	public function actionGetProductsByCategoryId($id) {
    	$products[] = $this->products(1);
    	$products[] = $this->products(2);
    	$products[] = $this->products(5);

        return $this->asJson($products);
	}

	private function products($id = null) {
		$response = [];
		$response[] = [
			'id'=> 1,
			'status'=> 1,
			'name'=> 'Мыло',
			'description'=> 'Мыло пахучее',
			'barcode'=> '123456789',
			'pathToImage'=> 'http://wms.nmdx.kz/mobile/dessert3.jpg',
		];

		$response[] = [
			'id'=> 2,
			'status'=> 2,
			'name'=> 'Мыло 2',
			'description'=> 'Мыло пахучее 2',
			'barcode'=> '1234567890',
			'pathToImage'=> 'http://wms.nmdx.kz/mobile/soap01.jpg',
		];
		$response[] = [
			'id'=> 3,
			'status'=> 3,
			'name'=> 'Мыло 3',
			'description'=> 'Мыло пахучее 3',
			'barcode'=> '1234567891',
			'pathToImage'=> 'http://wms.nmdx.kz/mobile/soap02.jpg',
		];

		$response[] = [
			'id'=> 4,
			'status'=> 0,
			'name'=> 'Банан',
			'description'=> 'Банан пахучий',
			'barcode'=> '1234567892',
			'pathToImage'=> 'http://wms.nmdx.kz/mobile/soap03.jpg',
		];

		$response[] = [
			'id'=> 5,
			'status'=> 1,
			'name'=> 'Мыло 4',
			'description'=> 'Мыло пахучее 4',
			'barcode'=> '123456789',
			'pathToImage'=> 'http://wms.nmdx.kz/mobile/banan.jpg',
		];

		$response[] = [
			'id'=> 6,
			'status'=> 2,
			'name'=> 'Мыло 5',
			'description'=> 'Мыло пахучее 5',
			'barcode'=> '1234567890',
			'pathToImage'=> 'http://wms.nmdx.kz/mobile/banan.jpg',
		];
		$response[] = [
			'id'=> 7,
			'status'=> 3,
			'name'=> 'Мыло 6',
			'description'=> 'Мыло пахучее 6',
			'barcode'=> '1234567891',
			'pathToImage'=> 'http://wms.nmdx.kz/mobile/banan.jpg',
		];
		$response[] = [
			'id'=> 8,
			'status'=> 0,
			'name'=> 'Банан 2',
			'description'=> 'Банан пахучий 2',
			'barcode'=> '1234567892',
			'pathToImage'=> 'http://wms.nmdx.kz/mobile/no-photo.jpg',
		];

		if(!is_null($id) && isset($response[$id])) {
			return $response[$id];
		}

        return $response;
	}
}