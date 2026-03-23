<?php
namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\defacto\transfer\forms\TransferFormV2;
use common\ecommerce\defacto\transfer\repository\TransferRepositoryV2;
use common\ecommerce\defacto\transfer\service\TransferServiceV2;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\entities\EcommerceTransferItems;
use common\modules\store\models\Store;
use stockDepartment\components\Controller;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class TransferV2Controller extends Controller
{
	
    public function actionGetBatches()
    {
        //die('/ecommerce/defacto/transfer/get-batches DIE');

        $service = new TransferServiceV2();
        $batchIdList = $service->GetBatches();

        if(count($batchIdList) > 0) {
            Yii::$app->session->setFlash('info', "Новые трансферы: ".count($batchIdList).", были успешно загружены");
        } else {
            Yii::$app->session->setFlash('danger', "Нет новых трансферов");
        }

        return $this->redirect('/ecommerce/defacto/transfer-v2/all-pick-list');
    }
    
	
	
    public function actionAllPickList()
    {
        $service = new TransferServiceV2();
        return $this->render('all-pick-list',[
            'dataProvider'=>$service->getOrdersForPrintPickingList(),

        ]);
    }

    public function actionPrint($ids)
    {
        return $this->render('print/pick-list-pdf',[
            'outboundList'=>(new TransferServiceV2())->reservationOrdersForPrintPickingList([$ids])
        ]);
    }


    public function actionIndex()
    {
        $form = new TransferFormV2();
        return $this->render('index', ['model' => $form]);
    }

    /**
     *
     * */
    public function actionPickingListBarcode()
    {
        $errors = [];
        $expectedQty = 0;
        $acceptedQty = 0;

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_PIKING_LIST_BARCODE);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $result = $returnForm->scannedPrintPickListBarcode();
            $expectedQty = $result->expectedQty;
            $acceptedQty = $result->acceptedQty;
        } else {
            $errors = ActiveForm::validate($returnForm);
        }

        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'expectedQty'=> $expectedQty,
            'acceptedQty'=> $acceptedQty,
        ]);
    }

    /**
     *
     * */
    public function actionOurBoxBarcode()
    {
        $errors = [];
        $qtyProductsInBox = 0;
        $productExpectedQty = 0;
        $productAcceptedQty = 0;

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_OUR_BOX_BARCODE);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $result = $returnForm->scannedOurBoxBarcode();

            $productExpectedQty = $result->productExpectedQty;
            $productAcceptedQty = $result->productAcceptedQty;
        } else {
            $errors = ActiveForm::validate($returnForm);
        }

        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'qtyProductsInBox'=> $qtyProductsInBox,
            'productExpectedQty'=> $productExpectedQty,
            'productAcceptedQty'=> $productAcceptedQty,
        ]);
    }

    /**
     *
     * */
    public function actionLcBarcode()
    {
        $errors = [];
        $qtyProductsInBox = 0;
        $totalExpectedQty = 0;
        $totalAcceptedQty = 0;
        $productExpectedQty = 0;
        $productAcceptedQty = 0;

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_LC_BOX_BARCODE);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $result = $returnForm->scannedLcBarcode();
            $qtyProductsInBox = $result->qtyProductsInBox;
            $totalExpectedQty = $result->totalExpectedQty;
            $totalAcceptedQty = $result->totalAcceptedQty;
            $productExpectedQty = $result->productExpectedQty;
            $productAcceptedQty = $result->productAcceptedQty;

        } else {
            $errors = ActiveForm::validate($returnForm);
        }

        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'totalExpectedQty'=> $totalExpectedQty,
            'totalAcceptedQty'=> $totalAcceptedQty,
            'qtyProductsInBox'=> $qtyProductsInBox,
            'productExpectedQty'=> $productExpectedQty,
            'productAcceptedQty'=> $productAcceptedQty,
        ]);
    }

    /**
     *
     * */
    public function actionMoveAllProducts()
    {
        $errors = [];
        $qtyProductsInBox = 0;
        $totalExpectedQty = 0;
        $totalAcceptedQty = 0;
        $productExpectedQty = 0;
        $productAcceptedQty = 0;

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_MOVE_ALL_BOX_BARCODE);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $result = $returnForm->moveAllProductFromOurBox();
            $qtyProductsInBox = $result->qtyProductsInBox;
            $totalExpectedQty = $result->totalExpectedQty;
            $totalAcceptedQty = $result->totalAcceptedQty;
            $productExpectedQty = $result->productExpectedQty;
            $productAcceptedQty = $result->productAcceptedQty;

        } else {
            $errors = ActiveForm::validate($returnForm);
        }

        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'totalExpectedQty'=> $totalExpectedQty,
            'totalAcceptedQty'=> $totalAcceptedQty,
            'qtyProductsInBox'=> $qtyProductsInBox,
            'productExpectedQty'=> $productExpectedQty,
            'productAcceptedQty'=> $productAcceptedQty,
        ]);
    }

    /**
     *
    * */
    public function actionProductBarcode()
    {
        $errors = [];
        $productExpectedQty = 0;
        $productAcceptedQty = 0;
        $totalExpectedQty = 0;
        $totalAcceptedQty = 0;
        $qtyProductsInBox = 0;

        $productExpectedQtyInBox = 0;
        $productAcceptedQtyInBox = 0;

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_PRODUCT_BARCODE);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $result = $returnForm->scannedProductBarcode();
            $productExpectedQty = $result->productExpectedQty;
            $productAcceptedQty = $result->productAcceptedQty;
            $totalExpectedQty = $result->totalExpectedQty;
            $totalAcceptedQty = $result->totalAcceptedQty;
            $qtyProductsInBox = $result->qtyProductsInBox;
            $productExpectedQtyInBox = $result->productExpectedQtyInBox;
            $productAcceptedQtyInBox = $result->productAcceptedQtyInBox;

        } else {
            $errors = ActiveForm::validate($returnForm);
        }

        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'productExpectedQty'=> $productExpectedQty,
            'productAcceptedQty'=> $productAcceptedQty,
            'totalExpectedQty'=> $totalExpectedQty,
            'totalAcceptedQty'=> $totalAcceptedQty,
            'qtyProductsInBox'=> $qtyProductsInBox,

            'productExpectedQtyInBox'=> $productExpectedQtyInBox,
            'productAcceptedQtyInBox'=> $productAcceptedQtyInBox,
        ]);
    }

    //
    public function actionShowBoxItems()
    { // show-box-items

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_SHOW_BOX_ITEMS);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            return $this->asJson([
                'success' => 'Y',
                'items' => $this->renderPartial('_show-box-items', ['items' => $returnForm->showBoxItems(),'dto'=>$returnForm->getDTO()]),
            ]);
        }

        $errors = ActiveForm::validate($returnForm);
        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ]);
    }

    //
    public function actionShowLcBoxItems()
    { // show-lc-box-items

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_SHOW_LC_BOX_ITEMS);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            return $this->asJson([
                'success' => 'Y',
                'items' => $this->renderPartial('_show-box-items', ['items' => $returnForm->showLcBoxItems(),'dto'=>$returnForm->getDTO()]),
            ]);
        }

        $errors = ActiveForm::validate($returnForm);
        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ]);
    }
    //
    public function actionShowScannedItems()
    { // show-scanned-items

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_SHOW_SCANNED_ITEMS);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            return $this->asJson([
                'success' => 'Y',
                'items' => $this->renderPartial('_show_scanned-items', ['items' => $returnForm->showScannedItems(),'dto'=>$returnForm->getDTO()]),
            ]);
        }

        $errors = ActiveForm::validate($returnForm);
        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ]);
    }

    //
    public function actionShowOrderItems()
    { // show-picking-list-items

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_SHOW_ORDER_ITEMS);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            return $this->asJson([
                'success' => 'Y',
                'items' => $this->renderPartial('_show_order-items', ['items' => $returnForm->showOrderItems(),'dto'=>$returnForm->getDTO()]),
            ]);
        }

        $errors = ActiveForm::validate($returnForm);
        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ]);
    }

    //
    public function actionEmptyBox()
    { // empty-box
        $errors = [];
        $productExpectedQty = 0;
        $productAcceptedQty = 0;
        $totalExpectedQty = 0;
        $totalAcceptedQty = 0;
        $qtyProductsInBox = 0;

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_EMPTY_BOX);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $result = $returnForm->emptyBox();
            $productExpectedQty = $result->productExpectedQty;
            $productAcceptedQty = $result->productAcceptedQty;
            $totalExpectedQty = $result->totalExpectedQty;
            $totalAcceptedQty = $result->totalAcceptedQty;
            $qtyProductsInBox = $result->qtyProductsInBox;

        } else {
            $errors = ActiveForm::validate($returnForm);
        }

        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'productExpectedQty'=> $productExpectedQty,
            'productAcceptedQty'=> $productAcceptedQty,
            'totalExpectedQty'=> $totalExpectedQty,
            'totalAcceptedQty'=> $totalAcceptedQty,
            'qtyProductsInBox'=> $qtyProductsInBox,
        ]);
    }

    //
    public function actionComplete()
    { // complete
        $errors = [];

        $returnForm = new TransferFormV2();
        $returnForm->setScenario(TransferFormV2::SCENARIO_COMPLETE_ORDER);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $result = $returnForm->complete();

        } else {
            $errors = ActiveForm::validate($returnForm);
        }
//        die;
        return $this->asJson([
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
        ]);
    }
    public function actionTestReserve($id)
    {
        $tr = new TransferRepositoryV2();
        $orderInfo = $tr->getOrderInfo($id);

        if($orderInfo->order->status == \common\ecommerce\constants\TransferStatus::_NEW) {
            $tr->resetTransferOrder($id);
            EcommerceStock::updateAll(['transfer_box_check_step'=>''],['transfer_box_check_step'=>$orderInfo->order->id]);
        } else {
            Yii::$app->session->setFlash('danger', "Для это заказа уже напечатали лист сборки");
            return $this->redirect('all-pick-list');
        }

        $result = [];
        foreach($orderInfo->boxItems as $ourBoxBarcode) {
            $availableProductInBox = EcommerceStock::find()
                ->select('place_address_barcode, box_address_barcode, product_barcode, count(product_barcode) as productQty, product_season_full, client_product_sku')
                ->andWhere([
                    'client_id' => 2,
                    'box_address_barcode' => $ourBoxBarcode,
                    'status_availability' => StockAvailability::YES,
                    'condition_type' => StockConditionType::UNDAMAGED,
                ])
                ->groupBy('product_barcode, box_address_barcode, place_address_barcode,client_product_sku')
                ->asArray()
                ->all();

            $item = [];
            $item['isProblemBox'] = 'NO';
            $item['expectedBoxBarcode'] = $ourBoxBarcode;
            $item['readyProductQtyToTransfer'] = 0;
            $item['readyProductQtyToMoveOtherBox'] = 0;
            $item['sorting'] = 0;
            $item['availableProductInBox'] = $availableProductInBox;

            $result[] = $item;
        }

        foreach($result as $i=>$availableProductInBox) {
            foreach ($availableProductInBox['availableProductInBox'] as $i2 => $product) {
                $expectedProductInfo = EcommerceTransferItems::find()->andWhere(['client_SkuId' => $product['client_product_sku'],'transfer_id' => $orderInfo->order->id])->asArray()->one();

                $result[$i]['availableProductInBox'][$i2]['isExistProductBarcode'] = 'NO';
                $result[$i]['availableProductInBox'][$i2]['expectedProductQty'] = 0;
                $result[$i]['availableProductInBox'][$i2]['sorting'] = 0;

                if(!empty($expectedProductInfo)) {
                    $result[$i]['readyProductQtyToTransfer'] += $product['productQty'];
                    $result[$i]['availableProductInBox'][$i2]['isExistProductBarcode'] = 'YES';
                    $result[$i]['availableProductInBox'][$i2]['expectedProductQty'] = $expectedProductInfo['expected_qty'];

                    EcommerceStock::updateAll([
                        'transfer_box_check_step'=>$orderInfo->order->id
                    ],[
                        'client_id' => 2,
                        'box_address_barcode' => $availableProductInBox['expectedBoxBarcode'],
                        'status_availability' => StockAvailability::YES,
                        'condition_type' => StockConditionType::UNDAMAGED,
                    ]);

                } else {
                    $result[$i]['sorting'] += 1;
                    $result[$i]['readyProductQtyToMoveOtherBox'] += $product['productQty'];
                    $result[$i]['isProblemBox'] = 'YES';
                }
            }
        }

        $fileName = 'test-transfer-reserv.csv';
        $rowHeader = 'Place_address_barcode'.';'.
            'Box_address_barcode'.';'.
            'Product_barcode'.';'.
            'ProductQty'.';'.
            'Product_season_full'.';'.
            'isExistProductBarcode'.';'.
            'ExpectedProductQty'.';'.
            'Sorting'.';'.
            'isProblemBox'.';';

        file_put_contents($fileName,$rowHeader."\n");
        ArrayHelper::multisort($result,'sorting');
        $forPDF = [];
        foreach($result as $i=>$availableProductInBox) {

            if(empty($availableProductInBox['availableProductInBox'])) {
                continue;
            }

            ArrayHelper::multisort($availableProductInBox['availableProductInBox'],'expectedProductQty');

            foreach ($availableProductInBox['availableProductInBox'] as $i2 => $product) {

                if(!empty($product['product_barcode'])) {

                    $availableSeason = EcommerceStock::find()->select('product_season_year')->andWhere(['product_barcode'=>$product['product_barcode']])->scalar();
                    $isProblemBox = $availableProductInBox['isProblemBox'];
                    $isExistProductBarcode = $product['isExistProductBarcode'];
                    $moveToOtherBox = $isExistProductBarcode == 'NO' ? 'ДА' : 'НЕТ';
                    if(in_array($availableSeason,['19_','20_'])) {
                        $isProblemBox = 'YES';
                        $isExistProductBarcode = 'NO';
                        $moveToOtherBox = 'ДА';
                    }


                    if($isProblemBox == 'NO') {
                        continue;
                    }

                    $forPDF [$product['box_address_barcode']][] =  [
                        'place_address_barcode' => $product['place_address_barcode'],
                        'box_address_barcode' => $product['box_address_barcode'],
                        'product_barcode' => $product['product_barcode'],
                        'productQty' => $product['productQty'],
                        'product_season_full' => $product['product_season_full'],
                        'isExistProductBarcode' => $isExistProductBarcode,
                        'expectedProductQty' => $product['expectedProductQty'],
                        'sorting' => $availableProductInBox['sorting'],
                        'isProblemBox' =>$isProblemBox,
                        'moveToOtherBox' =>$moveToOtherBox,
                    ];
                }
            }
        }

//        VarDumper::dump(count($result), 10, true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
        //VarDumper::dump($forPDF, 10, true);
        //die(' END OK ');
        if(empty($forPDF)) {
            return $this->redirect(['print', 'ids' => $orderInfo->order->id]);
        }


        return $this->render('print/check-pick-list-pdf',['transferList'=>$forPDF,'orderInfo'=>$orderInfo]);
    }

    public function actionTestReserve_Serdar($id)
    {
        $tr = new TransferRepositoryV2();
        $orderInfo = $tr->getOrderInfo($id);

        if($orderInfo->order->status == \common\ecommerce\constants\TransferStatus::_NEW) {
            $tr->resetTransferOrder($id);
            EcommerceStock::updateAll(['transfer_box_check_step'=>''],['transfer_box_check_step'=>$orderInfo->order->id]);
        } else {
            Yii::$app->session->setFlash('danger', "Для это заказа уже напечатали лист сборки");
            return $this->redirect('all-pick-list');
        }

        $result = [];
        foreach($orderInfo->boxItems as $ourBoxBarcode) {
            $availableProductInBox = EcommerceStock::find()
                ->select('place_address_barcode, box_address_barcode, product_barcode, count(product_barcode) as productQty, product_season_full, client_product_sku')
                ->andWhere([
                    'client_id' => 2,
                    'box_address_barcode' => $ourBoxBarcode,
                    'note_message2' => 'transfer2',
//                    'status_availability' => StockAvailability::YES,
//                    'condition_type' => StockConditionType::UNDAMAGED,
                ])
                ->groupBy('product_barcode, box_address_barcode, place_address_barcode,client_product_sku')
                ->asArray()
                ->all();

            $item = [];
            $item['isProblemBox'] = 'NO';
            $item['expectedBoxBarcode'] = $ourBoxBarcode;
            $item['readyProductQtyToTransfer'] = 0;
            $item['readyProductQtyToMoveOtherBox'] = 0;
            $item['sorting'] = 0;
            $item['availableProductInBox'] = $availableProductInBox;

            $result[] = $item;
        }

        foreach($result as $i=>$availableProductInBox) {
            foreach ($availableProductInBox['availableProductInBox'] as $i2 => $product) {
                $expectedProductInfo = EcommerceTransferItems::find()->andWhere(['client_SkuId' => $product['client_product_sku'],'transfer_id' => $orderInfo->order->id])->asArray()->one();

                $result[$i]['availableProductInBox'][$i2]['isExistProductBarcode'] = 'NO';
                $result[$i]['availableProductInBox'][$i2]['expectedProductQty'] = 0;
                $result[$i]['availableProductInBox'][$i2]['sorting'] = 0;

                if(!empty($expectedProductInfo)) {
                    $result[$i]['readyProductQtyToTransfer'] += $product['productQty'];
                    $result[$i]['availableProductInBox'][$i2]['isExistProductBarcode'] = 'YES';
                    $result[$i]['availableProductInBox'][$i2]['expectedProductQty'] = $expectedProductInfo['expected_qty'];

                    EcommerceStock::updateAll([
                        'transfer_box_check_step'=>$orderInfo->order->id
                    ],[
                        'client_id' => 2,
                        'box_address_barcode' => $availableProductInBox['expectedBoxBarcode'],
                        'note_message2' => 'transfer2',
//                        'status_availability' => StockAvailability::YES,
//                        'condition_type' => StockConditionType::UNDAMAGED,

                    ]);

                } else {
                    $result[$i]['sorting'] += 1;
                    $result[$i]['readyProductQtyToMoveOtherBox'] += $product['productQty'];
                    $result[$i]['isProblemBox'] = 'YES';
                }
            }
        }

        $fileName = 'test-transfer-reserv.csv';
        $rowHeader = 'Place_address_barcode'.';'.
            'Box_address_barcode'.';'.
            'Product_barcode'.';'.
            'ProductQty'.';'.
            'Product_season_full'.';'.
            'isExistProductBarcode'.';'.
            'ExpectedProductQty'.';'.
            'Sorting'.';'.
            'isProblemBox'.';';

        file_put_contents($fileName,$rowHeader."\n");
        ArrayHelper::multisort($result,'sorting');
        $forPDF = [];
        foreach($result as $i=>$availableProductInBox) {

            if(empty($availableProductInBox['availableProductInBox'])) {
                continue;
            }

            ArrayHelper::multisort($availableProductInBox['availableProductInBox'],'expectedProductQty');

            foreach ($availableProductInBox['availableProductInBox'] as $i2 => $product) {

                if(!empty($product['product_barcode'])) {

                    $availableSeason = EcommerceStock::find()->select('product_season_year')->andWhere(['product_barcode'=>$product['product_barcode']])->scalar();
                    $isProblemBox = $availableProductInBox['isProblemBox'];
                    $isExistProductBarcode = $product['isExistProductBarcode'];
                    $moveToOtherBox = $isExistProductBarcode == 'NO' ? 'ДА' : 'НЕТ';
                    if(in_array($availableSeason,[19,20])) {
                        $isProblemBox = 'YES';
                        $isExistProductBarcode = 'NO';
                        $moveToOtherBox = 'ДА';
                    }


                    if($isProblemBox == 'NO') {
                        continue;
                    }

                    $forPDF [$product['box_address_barcode']][] =  [
                        'place_address_barcode' => $product['place_address_barcode'],
                        'box_address_barcode' => $product['box_address_barcode'],
                        'product_barcode' => $product['product_barcode'],
                        'productQty' => $product['productQty'],
                        'product_season_full' => $product['product_season_full'],
                        'isExistProductBarcode' => $isExistProductBarcode,
                        'expectedProductQty' => $product['expectedProductQty'],
                        'sorting' => $availableProductInBox['sorting'],
                        'isProblemBox' =>$isProblemBox,
                        'moveToOtherBox' =>$moveToOtherBox,
                    ];
                }
            }
        }

//        VarDumper::dump(count($result), 10, true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($forPDF, 10, true);
//        die(' END OK ');

        return $this->render('print/check-pick-list-pdf',['transferList'=>$forPDF,'orderInfo'=>$orderInfo]);
    }


    public function actionPreReserved($id) {
        $tr = new TransferRepositoryV2();
        $orderInfo = $tr->getOrderInfo($id);

        //if($orderInfo->order->status == \common\ecommerce\constants\TransferStatus::_NEW) {
         //  return $this->redirect(['test-reserve', 'id' => $orderInfo->order->id]);
        //}

        return $this->redirect(['print', 'ids' => $orderInfo->order->id]);
    }

    public function actionReset($id) {
    	// /ecommerce/defacto/transfer/reset?id=11359
        $tr = new TransferRepositoryV2();
        //$orderInfo = $tr->getOrderInfo($id);
		$tr->resetTransferOrder($id);

        return $this->redirect(['/ecommerce/defacto/transfer-report/index']);
    }

	public function actionPrintBoxLabel($id) {
		// /ecommerce/defacto/transfer-v2/print-box-label?id=11419
		$tr = new TransferRepositoryV2();
		$orderInfo = $tr->getOrderInfo($id);
		$toStore = Store::getStoreByShopCodeForECom($orderInfo->order->client_ToBusinessUnitId);
//		VarDumper::dump($orderInfo,10,true);
//		die;
		return $this->render("print/box-label-self-adhesive-pdf",[
			"orderInfo"=>$orderInfo,
			"toStore"=>$toStore,
		]);
	}
}